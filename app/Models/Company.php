<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = [
        'name',
        'registration_number',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'industry',
        'employee_count',
        'plan_type',
        'status',
        'created_by',
    ];

    protected $casts = [
        'employee_count' => 'integer',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function salaryStructures()
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
