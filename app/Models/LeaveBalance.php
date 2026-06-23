<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'leave_year',
        'opening_balance',
        'allocated_days',
        'used_days',
        'pending_days',
        'carry_forward_days',
        'encashed_days',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'allocated_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'pending_days' => 'decimal:2',
        'carry_forward_days' => 'decimal:2',
        'encashed_days' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }
}
