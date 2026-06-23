<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryStructure;
use App\Models\SalaryComponent;
use App\Models\TaxSlab;
use App\Models\ProfessionalTax;

class TaxCalculationService
{
    /**
     * Default Indian new-regime slabs for AY 2026-27.
     * Used as a fallback when the tax_slabs table has not been seeded yet.
     */
    private const DEFAULT_TAX_SLABS = [
        ['income_from' => 0, 'income_to' => 400000, 'tax_rate' => 0, 'description' => 'No tax up to 4 lakhs'],
        ['income_from' => 400000, 'income_to' => 800000, 'tax_rate' => 5, 'description' => '5% on income between 4 to 8 lakhs'],
        ['income_from' => 800000, 'income_to' => 1200000, 'tax_rate' => 10, 'description' => '10% on income between 8 to 12 lakhs'],
        ['income_from' => 1200000, 'income_to' => 1600000, 'tax_rate' => 15, 'description' => '15% on income between 12 to 16 lakhs'],
        ['income_from' => 1600000, 'income_to' => 2000000, 'tax_rate' => 20, 'description' => '20% on income between 16 to 20 lakhs'],
        ['income_from' => 2000000, 'income_to' => 2400000, 'tax_rate' => 25, 'description' => '25% on income between 20 to 24 lakhs'],
        ['income_from' => 2400000, 'income_to' => 9999999999, 'tax_rate' => 30, 'description' => '30% on income above 24 lakhs'],
    ];

    /**
     * Calculate salary components based on salary structure
     */
    public function calculateSalaryComponents(Employee $employee, $baseSalary)
    {
        $salaryAssignment = $employee->salaries()
            ->with('salaryStructure')
            ->latest('effective_from')
            ->first();

        $salaryStructure = $salaryAssignment?->salaryStructure;

        if (!$salaryStructure) {
            return [
                'base' => $baseSalary,
                'hra' => 0,
                'allowances' => 0,
                'ta' => 0,
                'da' => 0,
                'gross' => $baseSalary,
            ];
        }

        $components = SalaryComponent::where('salary_structure_id', $salaryStructure->id)
            ->where('component_type', 'earning')
            ->where('is_active', true)
            ->get();

        $earnings = [
            'base' => $baseSalary,
            'hra' => (float) ($salaryStructure->hra ?? 0),
            'allowances' => (float) ($salaryStructure->allowances ?? 0),
        ];

        foreach ($components as $component) {
            $amount = $this->getComponentAmount($component, $baseSalary);
            $earnings[strtolower(str_replace(' ', '_', $component->component_name))] = $amount;
        }

        $grossSalary = array_sum($earnings);

        return [
            ...$earnings,
            'gross' => $grossSalary,
        ];
    }

    /**
     * Calculate income tax based on Indian tax slabs
     */
    public function calculateIncomeTax($annualGrossSalary, $deductionsAmount = 0)
    {
        return $this->calculateIncomeTaxBreakdown($annualGrossSalary, $deductionsAmount)['tax'];
    }

    /**
     * Calculate income tax and return the slab-by-slab breakdown.
     */
    public function calculateIncomeTaxBreakdown($annualGrossSalary, $deductionsAmount = 0): array
    {
        $taxableIncome = max(0, $annualGrossSalary - $deductionsAmount);
        $taxSlabs = $this->getActiveTaxSlabs();

        $tax = 0;
        $appliedSlabs = [];

        foreach ($taxSlabs as $slab) {
            if ($taxableIncome <= $slab->income_from) {
                continue;
            }

            $taxableInThisSlab = min($taxableIncome, $slab->income_to) - $slab->income_from;

            if ($taxableInThisSlab <= 0) {
                continue;
            }

            $slabTax = round(($taxableInThisSlab * $slab->tax_rate) / 100, 2);
            $tax += $slabTax;

            $appliedSlabs[] = [
                'income_from' => (float) $slab->income_from,
                'income_to' => (float) $slab->income_to,
                'tax_rate' => (float) $slab->tax_rate,
                'taxable_amount' => round($taxableInThisSlab, 2),
                'tax_amount' => $slabTax,
                'description' => $slab->description,
            ];

            if ($taxableIncome <= $slab->income_to) {
                break;
            }
        }

        return [
            'taxable_income' => round($taxableIncome, 2),
            'tax' => round($tax, 2),
            'slabs' => $appliedSlabs,
        ];
    }

    /**
     * Calculate professional tax based on state and salary
     */
    public function calculateProfessionalTax($annualGrossSalary, $state = 'General')
    {
        $profTax = ProfessionalTax::where('state', $state)
            ->where('is_active', true)
            ->where('income_from', '<=', $annualGrossSalary)
            ->where('income_to', '>=', $annualGrossSalary)
            ->first();

        return $profTax ? $profTax->tax_amount : 0;
    }

    /**
     * Calculate PF deduction (typically 12% of basic salary)
     */
    public function calculatePFDeduction($baseSalary, $pfPercentage = 12)
    {
        return round(($baseSalary * $pfPercentage) / 100, 2);
    }

    /**
     * Calculate ESIC deduction for eligible wages.
     */
    public function calculateEsicDeduction($wageBase, $employeeRate = 0.75, $employerRate = 3.25, $wageCeiling = 21000): array
    {
        $wageBase = max(0, (float) $wageBase);
        $isApplicable = $wageBase > 0 && $wageBase <= $wageCeiling;

        $employeeShare = $isApplicable ? round(($wageBase * $employeeRate) / 100, 2) : 0;
        $employerShare = $isApplicable ? round(($wageBase * $employerRate) / 100, 2) : 0;

        return [
            'applicable' => $isApplicable,
            'wage_base' => round($wageBase, 2),
            'employee_rate' => $employeeRate,
            'employer_rate' => $employerRate,
            'employee_share' => $employeeShare,
            'employer_share' => $employerShare,
            'wage_ceiling' => $wageCeiling,
        ];
    }

    /**
     * Calculate total deductions
     */
    public function calculateTotalDeductions(Employee $employee, $grossSalary, $baseSalary, $annualGrossSalary, $state = 'General', $pfWageBase = null, $esicWageBase = null)
    {
        $incomeTaxBreakdown = $this->calculateIncomeTaxBreakdown($annualGrossSalary, 50000); // Standard deduction
        $incomeTax = $incomeTaxBreakdown['tax'];
        $professionalTax = $this->calculateProfessionalTax($annualGrossSalary, $state);
        $pfDeduction = $this->calculatePFDeduction($pfWageBase ?? $baseSalary);
        $esicBreakdown = $this->calculateEsicDeduction($esicWageBase ?? $grossSalary);
        $esicDeduction = $esicBreakdown['employee_share'];

        // Get other deductions from salary components
        $salaryAssignment = $employee->salaries()
            ->with('salaryStructure')
            ->latest('effective_from')
            ->first();

        $salaryStructure = $salaryAssignment?->salaryStructure;
        $otherDeductions = 0;

        if ($salaryStructure) {
            $deductionComponents = SalaryComponent::where('salary_structure_id', $salaryStructure->id)
                ->where('component_type', 'deduction')
                ->where('is_active', true)
                ->get();

            foreach ($deductionComponents as $component) {
                $otherDeductions += $this->getComponentAmount($component, $baseSalary);
            }
        }

        $monthlyIncomeTax = round($incomeTax / 12, 2);
        $monthlyProfessionalTax = round($professionalTax / 12, 2);

        return [
            'taxable_income' => $incomeTaxBreakdown['taxable_income'],
            'income_tax_slabs' => $incomeTaxBreakdown['slabs'],
            'standard_deduction' => 50000,
            'income_tax' => $monthlyIncomeTax,
            'annual_income_tax' => $incomeTax,
            'professional_tax' => $monthlyProfessionalTax,
            'annual_professional_tax' => $professionalTax,
            'pf_deduction' => $pfDeduction,
            'pf_rate' => 12,
            'pf_wage_base' => round($pfWageBase ?? $baseSalary, 2),
            'esic_deduction' => $esicDeduction,
            'annual_esic_deduction' => round($esicDeduction * 12, 2),
            'esic_rate' => $esicBreakdown['employee_rate'],
            'esic_wage_base' => $esicBreakdown['wage_base'],
            'esic_applicable' => $esicBreakdown['applicable'],
            'esic_wage_ceiling' => $esicBreakdown['wage_ceiling'],
            'employer_esic_share' => $esicBreakdown['employer_share'],
            'other_deductions' => $otherDeductions,
            'total' => $monthlyIncomeTax + $monthlyProfessionalTax + $pfDeduction + $esicDeduction + $otherDeductions,
        ];
    }

    /**
     * Get component amount (fixed or percentage-based)
     */
    private function getComponentAmount($component, $baseSalary)
    {
        if ($component->fixed_amount) {
            return $component->fixed_amount;
        }

        return round(($baseSalary * $component->percentage) / 100, 2);
    }

    /**
     * Calculate net salary
     */
    public function calculateNetSalary($grossSalary, $totalDeductions)
    {
        return round($grossSalary - $totalDeductions, 2);
    }

    /**
     * Get tax breakdown details
     */
    public function getTaxBreakdown($employee, $grossSalary, $baseSalary, $annualGrossSalary, $state = 'General', $pfWageBase = null, $esicWageBase = null)
    {
        $deductions = $this->calculateTotalDeductions($employee, $grossSalary, $baseSalary, $annualGrossSalary, $state, $pfWageBase, $esicWageBase);

        return [
            'taxable_income' => $deductions['taxable_income'],
            'standard_deduction' => $deductions['standard_deduction'],
            'income_tax_slabs' => $deductions['income_tax_slabs'],
            'monthly_income_tax' => $deductions['income_tax'],
            'annual_income_tax' => $deductions['annual_income_tax'],
            'monthly_professional_tax' => $deductions['professional_tax'],
            'annual_professional_tax' => $deductions['annual_professional_tax'],
            'monthly_pf' => $deductions['pf_deduction'],
            'annual_pf' => round($deductions['pf_deduction'] * 12, 2),
            'pf_rate' => $deductions['pf_rate'],
            'pf_wage_base' => $deductions['pf_wage_base'],
            'monthly_esic' => $deductions['esic_deduction'],
            'annual_esic' => $deductions['annual_esic_deduction'],
            'esic_rate' => $deductions['esic_rate'],
            'esic_wage_base' => $deductions['esic_wage_base'],
            'esic_applicable' => $deductions['esic_applicable'],
            'esic_wage_ceiling' => $deductions['esic_wage_ceiling'],
            'employer_esic_share' => $deductions['employer_esic_share'],
            'other_deductions' => $deductions['other_deductions'],
            'total_monthly_deductions' => $deductions['total'],
            'gross_salary' => $grossSalary,
        ];
    }

    /**
     * Resolve active slabs from the database or fall back to current defaults.
     */
    private function getActiveTaxSlabs()
    {
        $taxSlabs = TaxSlab::where('is_active', true)
            ->orderBy('income_from', 'asc')
            ->get();

        if ($taxSlabs->isNotEmpty()) {
            return $taxSlabs;
        }

        return collect(self::DEFAULT_TAX_SLABS)->map(function (array $slab) {
            return (object) $slab;
        });
    }
}
