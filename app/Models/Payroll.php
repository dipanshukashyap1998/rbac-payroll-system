<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payrolls';

    protected $fillable = [
        'company_id',
        'month',
        'year',
        'status',
        'created_by',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payslips()
    {
        return $this->hasMany(Payslip::class, 'payroll_run_id');
    }
}
