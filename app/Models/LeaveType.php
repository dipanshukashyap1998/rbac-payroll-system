<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'code',
        'default_days',
        'is_paid',
        'carry_forward',
        'max_carry_forward_days',
        'requires_approval',
        'is_active',
        'description',
    ];

    protected $casts = [
        'default_days' => 'decimal:2',
        'is_paid' => 'boolean',
        'carry_forward' => 'boolean',
        'max_carry_forward_days' => 'decimal:2',
        'requires_approval' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function balances()
    {
        return $this->hasMany(LeaveBalance::class);
    }

    public function requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
