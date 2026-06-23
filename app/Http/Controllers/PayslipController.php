<?php

namespace App\Http\Controllers;

use App\Models\Payslip;
use App\Services\LeavePolicyService;
use App\Services\TaxCalculationService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PayslipController extends Controller
{
    public function __construct(
        private TaxCalculationService $taxService,
        private LeavePolicyService $leavePolicyService
    )
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isAdmin()) {
            $companyId = $user->ownedCompany()->value('id');

            $payslips = Payslip::query()
                ->with(['employee.user', 'payrollRun'])
                ->whereHas('payrollRun', fn ($query) => $query->where('company_id', $companyId))
                ->latest()
                ->paginate(12);

            return view('payslips.index', [
                'payslips' => $payslips,
                'isEmployeeView' => false,
            ]);
        }

        if ($user->isEmployee()) {
            $employee = $user->employeeProfile;

            $current = now();
            $previous = now()->subMonth();

            $payslips = Payslip::query()
                ->with(['payrollRun'])
                ->where('employee_id', $employee?->id)
                ->whereHas('payrollRun', function ($query) use ($current, $previous) {
                    $query->whereIn('status', ['processed', 'paid'])
                        ->where(function ($monthQuery) use ($current, $previous) {
                            $monthQuery
                                ->where(function ($q) use ($current) {
                                    $q->where('year', $current->year)
                                        ->where('month', $current->month);
                                })
                                ->orWhere(function ($q) use ($previous) {
                                    $q->where('year', $previous->year)
                                        ->where('month', $previous->month);
                                });
                        });
                })
                ->latest()
                ->get();

            return view('payslips.index', [
                'payslips' => $payslips,
                'isEmployeeView' => true,
            ]);
        }

        abort(403, 'You are not allowed to view payslips.');
    }

    public function show(Request $request, Payslip $payslip): View
    {
        $user = $request->user();

        $payslip->load(['employee.user', 'payrollRun']);
        $breakdown = $this->buildPayslipBreakdown($payslip);

        if ($user->isAdmin()) {
            $companyId = $user->ownedCompany()->value('id');

            abort_unless($payslip->payrollRun?->company_id === $companyId, 403, 'You are not allowed to view this payslip.');

            return view('payslips.show', [
                'payslip' => $payslip,
                'breakdown' => $breakdown,
                'isEmployeeView' => false,
            ]);
        }

        if ($user->isEmployee()) {
            abort_unless($payslip->employee_id === $user->employeeProfile?->id, 403, 'You are not allowed to view this payslip.');

            return view('payslips.show', [
                'payslip' => $payslip,
                'breakdown' => $breakdown,
                'isEmployeeView' => true,
            ]);
        }

        abort(403, 'You are not allowed to view payslips.');
    }

    private function buildPayslipBreakdown(Payslip $payslip): array
    {
        $taxBreakdown = $payslip->tax_breakdown ?? [];
        $leaveBreakdown = $payslip->leave_breakdown ?? [];
        $employee = $payslip->employee;
        $payroll = $payslip->payrollRun;

        $salaryAssignment = $employee?->salaries()
            ->with('salaryStructure.components')
            ->latest('effective_from')
            ->first();

        if ($salaryAssignment?->salaryStructure) {
            $salaryStructure = $salaryAssignment->salaryStructure;
            $baseSalary = (float) $salaryStructure->base_salary;
            $hra = (float) $salaryStructure->hra;
            $allowances = (float) $salaryStructure->allowances;
            $salaryComponents = $this->taxService->calculateSalaryComponents($employee, $baseSalary);
            $grossSalary = (float) ($salaryComponents['gross'] ?? 0);
            $ta = (float) ($salaryComponents['ta'] ?? 0);
            $da = (float) ($salaryComponents['da'] ?? 0);
            $pfWageBase = round($baseSalary + $da, 2);
            $esicWageBase = round(max(0, $grossSalary - $hra), 2);
            $annualGrossSalary = round($grossSalary * 12, 2);
            $state = $employee->company?->state ?? 'General';
            $esicBreakdown = $this->taxService->calculateEsicDeduction($esicWageBase);
            $deductions = $this->taxService->calculateTotalDeductions(
                $employee,
                $grossSalary,
                $baseSalary,
                $annualGrossSalary,
                $state,
                $pfWageBase,
                $esicWageBase
            );

            $incomeTaxMonthly = (float) $deductions['income_tax'];
            $professionalTaxMonthly = (float) $deductions['professional_tax'];
            $pfDeduction = (float) $deductions['pf_deduction'];
            $monthlyEsic = (float) $deductions['esic_deduction'];
            $otherDeductions = (float) $deductions['other_deductions'];

            $computedLeaveBreakdown = $payroll && $employee
                ? $this->leavePolicyService->calculateLeaveDeduction($employee, $payroll, $grossSalary)
                : [];

            $leaveDeductionDays = (float) ($computedLeaveBreakdown['leave_deduction_days'] ?? 0);
            $leaveDeductionAmount = (float) ($computedLeaveBreakdown['leave_deduction_amount'] ?? 0);
            $leaveDetails = [
                'days' => $leaveDeductionDays,
                'amount' => $leaveDeductionAmount,
                'daily_rate' => (float) ($computedLeaveBreakdown['daily_rate'] ?? 0),
                'period_start' => $computedLeaveBreakdown['period_start'] ?? null,
                'period_end' => $computedLeaveBreakdown['period_end'] ?? null,
                'unpaid_leave_days' => (float) ($computedLeaveBreakdown['unpaid_leave_days'] ?? 0),
                'extra_paid_leave_days' => (float) ($computedLeaveBreakdown['extra_paid_leave_days'] ?? 0),
                'unpaid_leave_details' => $computedLeaveBreakdown['unpaid_leave_details'] ?? [],
                'extra_paid_leave_details' => $computedLeaveBreakdown['extra_paid_leave_details'] ?? [],
            ];

            $totalDeductions = round($incomeTaxMonthly + $professionalTaxMonthly + $pfDeduction + $monthlyEsic + $otherDeductions + $leaveDeductionAmount, 2);
            $netSalary = round($grossSalary - $totalDeductions, 2);
        } else {
            $grossSalary = (float) ($payslip->gross_salary ?? 0);
            $hra = (float) ($payslip->hra ?? 0);
            $baseSalary = (float) ($payslip->base_salary ?? 0);
            $allowances = (float) ($payslip->allowances ?? 0);
            $ta = (float) ($payslip->ta ?? 0);
            $da = (float) ($payslip->da ?? 0);
            $pfWageBase = (float) ($taxBreakdown['pf_wage_base'] ?? ($baseSalary + $da));
            $esicWageBase = (float) ($taxBreakdown['esic_wage_base'] ?? max(0, $grossSalary - $hra));
            $esicBreakdown = $this->taxService->calculateEsicDeduction($esicWageBase);
            $monthlyEsic = (float) ($taxBreakdown['monthly_esic'] ?? $esicBreakdown['employee_share']);
            $otherDeductions = (float) ($payslip->deductions ?? 0);
            $incomeTaxMonthly = (float) ($payslip->income_tax ?? 0);
            $professionalTaxMonthly = (float) ($payslip->professional_tax ?? 0);
            $pfDeduction = (float) ($payslip->pf_deduction ?? 0);
            $leaveBreakdown = $payslip->leave_breakdown ?? [];
            $leaveDeductionDays = (float) ($payslip->leave_deduction_days ?? ($leaveBreakdown['leave_deduction_days'] ?? 0));
            $leaveDeductionAmount = (float) ($payslip->leave_deduction ?? ($leaveBreakdown['leave_deduction_amount'] ?? 0));
            $leaveDetails = [
                'days' => $leaveDeductionDays,
                'amount' => $leaveDeductionAmount,
                'daily_rate' => (float) ($leaveBreakdown['daily_rate'] ?? 0),
                'period_start' => $leaveBreakdown['period_start'] ?? null,
                'period_end' => $leaveBreakdown['period_end'] ?? null,
                'unpaid_leave_days' => (float) ($leaveBreakdown['unpaid_leave_days'] ?? 0),
                'extra_paid_leave_days' => (float) ($leaveBreakdown['extra_paid_leave_days'] ?? 0),
                'unpaid_leave_details' => $leaveBreakdown['unpaid_leave_details'] ?? [],
                'extra_paid_leave_details' => $leaveBreakdown['extra_paid_leave_details'] ?? [],
            ];
            $totalDeductions = round($incomeTaxMonthly + $professionalTaxMonthly + $pfDeduction + $monthlyEsic + $otherDeductions + $leaveDeductionAmount, 2);
            $netSalary = round($grossSalary - $totalDeductions, 2);
        }

        $annualGrossSalary = $grossSalary * 12;
        $standardDeduction = (float) ($taxBreakdown['standard_deduction'] ?? 50000);
        $taxableIncome = (float) ($taxBreakdown['taxable_income'] ?? max(0, $annualGrossSalary - $standardDeduction));

        return [
            'gross_salary' => $grossSalary,
            'base_salary' => $baseSalary,
            'hra' => $hra,
            'allowances' => $allowances,
            'ta' => $ta,
            'da' => $da,
            'annual_gross_salary' => $annualGrossSalary,
            'standard_deduction' => $standardDeduction,
            'taxable_income' => $taxableIncome,
            'income_tax_monthly' => $incomeTaxMonthly,
            'annual_income_tax' => (float) ($taxBreakdown['annual_income_tax'] ?? ($incomeTaxMonthly * 12)),
            'professional_tax_monthly' => $professionalTaxMonthly,
            'annual_professional_tax' => (float) ($taxBreakdown['annual_professional_tax'] ?? ($professionalTaxMonthly * 12)),
            'pf_deduction' => $pfDeduction,
            'pf_rate' => (float) ($taxBreakdown['pf_rate'] ?? 12),
            'pf_wage_base' => $pfWageBase,
            'leave_deduction_days' => $leaveDeductionDays,
            'leave_deduction' => $leaveDeductionAmount,
            'leave_breakdown' => $leaveDetails,
            'monthly_esic' => $monthlyEsic,
            'annual_esic' => (float) ($taxBreakdown['annual_esic'] ?? ($monthlyEsic * 12)),
            'esic_rate' => (float) ($taxBreakdown['esic_rate'] ?? $esicBreakdown['employee_rate']),
            'esic_wage_base' => $esicWageBase,
            'esic_applicable' => (bool) ($taxBreakdown['esic_applicable'] ?? $esicBreakdown['applicable']),
            'esic_wage_ceiling' => (float) ($taxBreakdown['esic_wage_ceiling'] ?? $esicBreakdown['wage_ceiling']),
            'employer_esic_share' => (float) ($taxBreakdown['employer_esic_share'] ?? $esicBreakdown['employer_share']),
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'annual_net_salary' => $netSalary * 12,
            'take_home_percent' => $grossSalary > 0 ? round(($netSalary / $grossSalary) * 100, 2) : 0,
            'income_tax_slabs' => $taxBreakdown['income_tax_slabs'] ?? [],
        ];
    }
}
