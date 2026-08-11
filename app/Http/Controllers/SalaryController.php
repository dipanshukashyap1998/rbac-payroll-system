<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeSalary;
use App\Models\Payroll;
use App\Models\SalaryComponent;
use App\Models\SalaryStructure;
use App\Services\LeavePolicyService;
use App\Services\TaxCalculationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SalaryController extends Controller
{
    public function __construct(
        private TaxCalculationService $taxService,
        private LeavePolicyService $leavePolicyService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isEmployee()) {
            $salaryAssignment = EmployeeSalary::query()
                ->with(['salaryStructure.company', 'salaryStructure.components'])
                ->whereHas('employee', fn ($query) => $query->where('user_id', $user->id))
                ->latest('effective_from')
                ->first();

            $latestPayroll = $user->employeeProfile?->payslips()
                ->with('payrollRun')
                ->latest('generated_at')
                ->first()?->payrollRun;

            $salaryBreakdown = $salaryAssignment
                ? $this->buildEmployeeSalaryBreakdown($user->employeeProfile, $salaryAssignment, $latestPayroll)
                : null;

            return view('salary_structures.employee', compact('salaryAssignment', 'salaryBreakdown'));
        }

        $companyId = $user->ownedCompany()->value('id');

        $salaryStructures = SalaryStructure::query()
            ->with('company')
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(10);

        return view('salary_structures.index', compact('salaryStructures'));
    }

    public function create(Request $request): View
    {
        abort_unless($request->user()->isAdmin(), 403, 'Only admin can create salary structures.');

        return view('salary_structures.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isAdmin(), 403, 'Only admin can create salary structures.');

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
        ]);

        SalaryStructure::query()->create([
            ...$data,
            'company_id' => $user->ownedCompany()->value('id'),
            'hra' => $data['hra'] ?? 0,
            'allowances' => $data['allowances'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
        ]);

        return redirect()->route('salary-structures.index')->with('status', 'Salary structure created successfully.');
    }

    public function edit(Request $request, SalaryStructure $salaryStructure): View
    {
        $this->authorizeSalaryStructureAccess($request, $salaryStructure);

        return view('salary_structures.edit', compact('salaryStructure'));
    }

    public function update(Request $request, SalaryStructure $salaryStructure): RedirectResponse
    {
        $this->authorizeSalaryStructureAccess($request, $salaryStructure);

        $data = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'hra' => ['nullable', 'numeric', 'min:0'],
            'allowances' => ['nullable', 'numeric', 'min:0'],
            'deductions' => ['nullable', 'numeric', 'min:0'],
        ]);

        $salaryStructure->update([
            ...$data,
            'hra' => $data['hra'] ?? 0,
            'allowances' => $data['allowances'] ?? 0,
            'deductions' => $data['deductions'] ?? 0,
        ]);

        return redirect()->route('salary-structures.index')->with('status', 'Salary structure updated successfully.');
    }

    public function destroy(Request $request, SalaryStructure $salaryStructure): RedirectResponse
    {
        $this->authorizeSalaryStructureAccess($request, $salaryStructure);

        $salaryStructure->delete();

        return redirect()->route('salary-structures.index')->with('status', 'Salary structure deleted successfully.');
    }

    private function authorizeSalaryStructureAccess(Request $request, SalaryStructure $salaryStructure): void
    {
        $user = $request->user();

        if (! $user->isAdmin()) {
            abort(403, 'Only admin can manage salary structures.');
        }

        $companyId = $user->ownedCompany()->value('id');

        if ((int) $salaryStructure->company_id !== (int) $companyId) {
            abort(403, 'You can only manage salary structures for your own company.');
        }
    }

    private function buildEmployeeSalaryBreakdown(?Employee $employee, EmployeeSalary $salaryAssignment, ?Payroll $payroll = null): ?array
    {
        if (! $employee || ! $salaryAssignment->salaryStructure) {
            return null;
        }

        $salaryStructure = $salaryAssignment->salaryStructure;
        $earningComponents = $salaryStructure->components->where('component_type', 'earning')->where('is_active', true)->values();
        $deductionComponents = $salaryStructure->components->where('component_type', 'deduction')->where('is_active', true)->values();

        $baseSalary = (float) $salaryStructure->base_salary;
        $hra = (float) $salaryStructure->hra;
        $allowances = (float) $salaryStructure->allowances;
        $configuredDeductions = (float) $salaryStructure->deductions;

        $componentRows = $earningComponents->map(function (SalaryComponent $component) use ($baseSalary) {
            $amount = $this->resolveComponentAmount($component, $baseSalary);

            return [
                'name' => $component->component_name,
                'type' => $component->component_type,
                'amount' => $amount,
            ];
        })->values();

        $deductionRows = $deductionComponents->map(function (SalaryComponent $component) use ($baseSalary) {
            $amount = $this->resolveComponentAmount($component, $baseSalary);

            return [
                'name' => $component->component_name,
                'type' => $component->component_type,
                'amount' => $amount,
            ];
        })->values();

        $earningComponentsTotal = (float) $componentRows->sum('amount');
        $deductionComponentsTotal = (float) $deductionRows->sum('amount');

        $grossSalary = round($baseSalary + $hra + $allowances + $earningComponentsTotal, 2);
        $daAmount = $this->findComponentAmount($componentRows, ['DA', 'DEARNESS ALLOWANCE']);
        $taAmount = $this->findComponentAmount($componentRows, ['TA', 'TRAVEL ALLOWANCE', 'TRANSPORT ALLOWANCE']);
        $pfWageBase = round($baseSalary + $daAmount, 2);
        $esicWageBase = round(max(0, $grossSalary - $hra), 2);
        $annualGrossSalary = round($grossSalary * 12, 2);
        $state = $employee->company?->state ?? 'General';

        $incomeTaxBreakdown = $this->taxService->calculateIncomeTaxBreakdown($annualGrossSalary, 50000);
        $professionalTax = (float) $this->taxService->calculateProfessionalTax($annualGrossSalary, $state);
        $pfDeduction = (float) $this->taxService->calculatePFDeduction($pfWageBase);
        $esicBreakdown = $this->taxService->calculateEsicDeduction($esicWageBase);
        $employeeEsic = (float) $esicBreakdown['employee_share'];

        $monthlyIncomeTax = round($incomeTaxBreakdown['tax'] / 12, 2);
        $otherDeductions = round($configuredDeductions + $deductionComponentsTotal, 2);
        $payrollContext = $payroll ?? new Payroll([
            'month' => (int) now()->month,
            'year' => (int) now()->year,
        ]);
        $leaveDeduction = $this->leavePolicyService->calculateLeaveDeduction($employee, $payrollContext, $grossSalary);
        $totalDeductions = round($monthlyIncomeTax + $professionalTax + $pfDeduction + $employeeEsic + $otherDeductions + $leaveDeduction['leave_deduction_amount'], 2);
        $netSalary = round($grossSalary - $totalDeductions, 2);

        return [
            'base_salary' => $baseSalary,
            'hra' => $hra,
            'allowances' => $allowances,
            'gross_salary' => $grossSalary,
            'annual_gross_salary' => $annualGrossSalary,
            'earning_components' => $componentRows,
            'deduction_components' => $deductionRows,
            'da_amount' => $daAmount,
            'ta_amount' => $taAmount,
            'pf_wage_base' => $pfWageBase,
            'pf_deduction' => $pfDeduction,
            'esic_wage_base' => $esicWageBase,
            'esic_deduction' => $employeeEsic,
            'esic_employer_share' => (float) $esicBreakdown['employer_share'],
            'esic_applicable' => (bool) $esicBreakdown['applicable'],
            'professional_tax' => $professionalTax,
            'income_tax_monthly' => $monthlyIncomeTax,
            'income_tax_annual' => round($incomeTaxBreakdown['tax'], 2),
            'income_tax_slabs' => $incomeTaxBreakdown['slabs'],
            'leave_deduction_days' => $leaveDeduction['leave_deduction_days'],
            'leave_deduction' => $leaveDeduction['leave_deduction_amount'],
            'leave_breakdown' => $leaveDeduction,
            'other_deductions' => $otherDeductions,
            'total_deductions' => $totalDeductions,
            'net_salary' => $netSalary,
            'annual_net_salary' => round($netSalary * 12, 2),
            'take_home_percent' => $grossSalary > 0 ? round(($netSalary / $grossSalary) * 100, 2) : 0,
            'standard_deduction' => 50000,
            'state' => $state,
        ];
    }

    private function resolveComponentAmount(SalaryComponent $component, float $baseSalary): float
    {
        if ($component->fixed_amount !== null) {
            return (float) $component->fixed_amount;
        }

        return round(($baseSalary * (float) $component->percentage) / 100, 2);
    }

    private function findComponentAmount($componentRows, array $names): float
    {
        $component = $componentRows->first(function (array $row) use ($names) {
            return in_array(strtoupper(trim((string) $row['name'])), $names, true);
        });

        return (float) ($component['amount'] ?? 0);
    }
}
