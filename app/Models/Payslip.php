<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    protected $fillable = [
        'payroll_run_id',
        'employee_id',
        'base_salary',
        'hra',
        'allowances',
        'ta',
        'da',
        'gross_salary',
        'income_tax',
        'professional_tax',
        'pf_deduction',
        'leave_deduction_days',
        'leave_deduction',
        'deductions',
        'total_deductions',
        'net_salary',
        'leave_breakdown',
        'tax_breakdown',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'base_salary' => 'decimal:2',
        'hra' => 'decimal:2',
        'allowances' => 'decimal:2',
        'ta' => 'decimal:2',
        'da' => 'decimal:2',
        'gross_salary' => 'decimal:2',
        'income_tax' => 'decimal:2',
        'professional_tax' => 'decimal:2',
        'pf_deduction' => 'decimal:2',
        'leave_deduction_days' => 'decimal:2',
        'leave_deduction' => 'decimal:2',
        'deductions' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
        'leave_breakdown' => 'array',
        'tax_breakdown' => 'array',
    ];

    public function payrollRun()
    {
        return $this->belongsTo(Payroll::class, 'payroll_run_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
