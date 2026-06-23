<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Employee;
use App\Services\TaxCalculationService;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    private $taxService;

    public function __construct(TaxCalculationService $taxService)
    {
        $this->taxService = $taxService;
    }

    /**
     * Example: Generate payslip with tax calculations
     */
    public function generatePayslip(Payroll $payroll, Employee $employee)
    {
        // Get employee salary structure
        $employeeSalary = $employee->salary()->with('salaryStructure')->latest()->first();

        if (!$employeeSalary) {
            return redirect()->back()->with('error', 'Employee salary structure not configured');
        }

        $salaryStructure = $employeeSalary->salaryStructure;
        $baseSalary = $salaryStructure->base_salary;

        // Calculate salary components (HRA, Allowances, TA, DA, etc.)
        $components = $this->taxService->calculateSalaryComponents($employee, $baseSalary);

        $grossSalary = $components['gross'];
        $annualGrossSalary = $grossSalary * 12;

        // Get employee state (from address or user profile)
        $state = $employee->user->state ?? 'General'; // Adjust based on your data model

        // Calculate all deductions
        $deductions = $this->taxService->calculateTotalDeductions(
            $employee,
            $grossSalary,
            $baseSalary,
            $annualGrossSalary,
            $state,
            $baseSalary + ($components['da'] ?? 0)
        );

        // Calculate net salary
        $netSalary = $this->taxService->calculateNetSalary($grossSalary, $deductions['total']);

        // Get tax breakdown
        $taxBreakdown = $this->taxService->getTaxBreakdown(
            $employee,
            $grossSalary,
            $baseSalary,
            $annualGrossSalary,
            $state,
            $baseSalary + ($components['da'] ?? 0)
        );

        // Create or update payslip
        $payslip = $payroll->payslips()->updateOrCreate(
            ['employee_id' => $employee->id],
            [
                'base_salary' => $baseSalary,
                'hra' => $components['hra'] ?? 0,
                'allowances' => $components['allowances'] ?? 0,
                'ta' => $components['ta'] ?? 0,
                'da' => $components['da'] ?? 0,
                'gross_salary' => $grossSalary,
                'income_tax' => $deductions['income_tax'],
                'professional_tax' => $deductions['professional_tax'],
                'pf_deduction' => $deductions['pf_deduction'],
                'deductions' => $deductions['other_deductions'],
                'total_deductions' => $deductions['total'],
                'net_salary' => $netSalary,
                'tax_breakdown' => $taxBreakdown,
                'generated_at' => now(),
            ]
        );

        return view('payslips.show', compact('payslip', 'payroll'));
    }

    /**
     * Example: Generate payslips for entire payroll run
     */
    public function generatePayslips(Payroll $payroll)
    {
        $companies = $payroll->company()->get();

        foreach ($companies as $company) {
            $employees = Employee::where('company_id', $company->id)
                ->where('status', 'active')
                ->get();

            foreach ($employees as $employee) {
                $this->generatePayslip($payroll, $employee);
            }
        }

        return redirect()->route('payrolls.show', $payroll)
            ->with('status', 'Payslips generated successfully with tax calculations');
    }
}
