<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\Payroll;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LeavePolicyService
{
    public function defaultLeaveTypes(): array
    {
        return [
            ['name' => 'Earned Leave', 'code' => 'EL', 'default_days' => 18, 'is_paid' => true, 'carry_forward' => true, 'max_carry_forward_days' => 30, 'requires_approval' => true, 'description' => 'Planned leave accrued through the year.'],
            ['name' => 'Casual Leave', 'code' => 'CL', 'default_days' => 12, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Short planned or emergency leave.'],
            ['name' => 'Sick Leave', 'code' => 'SL', 'default_days' => 12, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Leave for illness or medical recovery.'],
            ['name' => 'Maternity Leave', 'code' => 'ML', 'default_days' => 182, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Maternity leave as per company policy.'],
            ['name' => 'Paternity Leave', 'code' => 'PL', 'default_days' => 15, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Paternity leave for new fathers.'],
            ['name' => 'Comp Off', 'code' => 'CO', 'default_days' => 7, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Compensatory off for extra working days.'],
            ['name' => 'Leave Without Pay', 'code' => 'LWP', 'default_days' => 0, 'is_paid' => false, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Unpaid leave when paid balance is exhausted.'],
            ['name' => 'Optional Holiday', 'code' => 'OH', 'default_days' => 2, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => false, 'description' => 'Festival or optional holiday leave.'],
            ['name' => 'Bereavement Leave', 'code' => 'BL', 'default_days' => 5, 'is_paid' => true, 'carry_forward' => false, 'max_carry_forward_days' => 0, 'requires_approval' => true, 'description' => 'Leave for family bereavement.'],
        ];
    }

    public function seedCompanyLeaveTypes(Company $company): Collection
    {
        return collect($this->defaultLeaveTypes())->map(function (array $data) use ($company) {
            return LeaveType::query()->firstOrCreate(
                [
                    'company_id' => $company->id,
                    'code' => $data['code'],
                ],
                [
                    'name' => $data['name'],
                    'default_days' => $data['default_days'],
                    'is_paid' => $data['is_paid'],
                    'carry_forward' => $data['carry_forward'],
                    'max_carry_forward_days' => $data['max_carry_forward_days'],
                    'requires_approval' => $data['requires_approval'],
                    'is_active' => true,
                    'description' => $data['description'],
                ]
            );
        });
    }

    public function ensureEmployeeBalances(Employee $employee, ?int $leaveYear = null): void
    {
        $leaveYear ??= (int) now()->year;
        $employee->loadMissing('company.leaveTypes');

        foreach ($employee->company?->leaveTypes()->where('is_active', true)->get() ?? [] as $leaveType) {
            $carryForward = $this->carryForwardDays($employee, $leaveType, $leaveYear);

            LeaveBalance::query()->firstOrCreate(
                [
                    'employee_id' => $employee->id,
                    'leave_type_id' => $leaveType->id,
                    'leave_year' => $leaveYear,
                ],
                [
                    'opening_balance' => $carryForward,
                    'allocated_days' => $leaveType->default_days,
                    'used_days' => 0,
                    'pending_days' => 0,
                    'carry_forward_days' => $carryForward,
                    'encashed_days' => 0,
                ]
            );
        }
    }

    public function calculateRequestedDays(Carbon $startDate, Carbon $endDate, ?string $sessionStart = null, ?string $sessionEnd = null): float
    {
        if ($startDate->isSameDay($endDate) && $sessionStart && $sessionEnd && $sessionStart !== $sessionEnd) {
            return 0.5;
        }

        $days = 0.0;
        $cursor = $startDate->copy()->startOfDay();
        $end = $endDate->copy()->startOfDay();

        while ($cursor->lte($end)) {
            if (! $cursor->isWeekend()) {
                $days += 1;
            }

            $cursor->addDay();
        }

        return max(0.5, $days);
    }

    public function availableBalance(Employee $employee, LeaveType $leaveType, int $leaveYear): float
    {
        $balance = LeaveBalance::query()
            ->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('leave_year', $leaveYear)
            ->first();

        if (! $balance) {
            return (float) $leaveType->default_days;
        }

        return round(($balance->opening_balance + $balance->allocated_days) - ($balance->used_days + $balance->pending_days + $balance->encashed_days), 2);
    }

    /**
     * Calculate deduction for leaves that exceed company-paid leave balance.
     */
    public function calculateLeaveDeduction(Employee $employee, Payroll $payroll, float $grossSalary): array
    {
        $employee->loadMissing('company');
        $periodStart = Carbon::create($payroll->year, $payroll->month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        $leaveYear = (int) $periodStart->year;

        $balances = LeaveBalance::query()
            ->with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('leave_year', $leaveYear)
            ->get();

        $monthlyRequests = LeaveRequest::query()
            ->with('leaveType')
            ->where('employee_id', $employee->id)
            ->where('company_id', $employee->company_id)
            ->whereDate('start_date', '<=', $periodEnd->toDateString())
            ->whereDate('end_date', '>=', $periodStart->toDateString())
            ->get();

        $unpaidLeaveDays = 0.0;
        $unpaidLeaveDetails = [];

        foreach ($monthlyRequests as $request) {
            $days = $this->overlapDays($request, $periodStart, $periodEnd);

            if ($request->status === 'approved' && ! $request->leaveType?->is_paid) {
                $unpaidLeaveDays += $days;
                $unpaidLeaveDetails[] = [
                    'leave_type' => $request->leaveType?->name ?? 'Unpaid Leave',
                    'days' => $days,
                    'reason' => $request->reason,
                ];
            }
        }

        $extraPaidLeaveDays = 0.0;
        $extraPaidLeaveDetails = [];

        foreach ($balances as $balance) {
            if (! $balance->leaveType?->is_paid) {
                continue;
            }

            $available = ($balance->opening_balance + $balance->allocated_days) - ($balance->used_days + $balance->pending_days + $balance->encashed_days);

            if ($available < 0) {
                $excessDays = abs((float) $available);
                $extraPaidLeaveDays += $excessDays;
                $extraPaidLeaveDetails[] = [
                    'leave_type' => $balance->leaveType?->name ?? 'Paid Leave',
                    'days' => round($excessDays, 2),
                ];
            }
        }

        $leaveDeductionDays = round($unpaidLeaveDays + $extraPaidLeaveDays, 2);
        $dailyRate = round($grossSalary / 30, 2);
        $leaveDeductionAmount = round($dailyRate * $leaveDeductionDays, 2);

        return [
            'leave_deduction_days' => $leaveDeductionDays,
            'leave_deduction_amount' => $leaveDeductionAmount,
            'daily_rate' => $dailyRate,
            'period_start' => $periodStart->toDateString(),
            'period_end' => $periodEnd->toDateString(),
            'unpaid_leave_days' => round($unpaidLeaveDays, 2),
            'extra_paid_leave_days' => round($extraPaidLeaveDays, 2),
            'unpaid_leave_details' => $unpaidLeaveDetails,
            'extra_paid_leave_details' => $extraPaidLeaveDetails,
        ];
    }

    private function carryForwardDays(Employee $employee, LeaveType $leaveType, int $leaveYear): float
    {
        if (! $leaveType->carry_forward) {
            return 0;
        }

        $previousBalance = LeaveBalance::query()
            ->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('leave_year', $leaveYear - 1)
            ->first();

        if (! $previousBalance) {
            return 0;
        }

        $remaining = max(0, ($previousBalance->opening_balance + $previousBalance->allocated_days) - ($previousBalance->used_days + $previousBalance->pending_days + $previousBalance->encashed_days));

        return round(min($remaining, $leaveType->max_carry_forward_days), 2);
    }

    private function overlapDays(LeaveRequest $request, Carbon $periodStart, Carbon $periodEnd): float
    {
        $requestStart = Carbon::parse($request->start_date)->startOfDay();
        $requestEnd = Carbon::parse($request->end_date)->startOfDay();
        $start = $requestStart->greaterThan($periodStart) ? $requestStart->copy() : $periodStart->copy();
        $end = $requestEnd->lessThan($periodEnd) ? $requestEnd->copy() : $periodEnd->copy();

        if ($start->gt($end)) {
            return 0;
        }

        if ($start->isSameDay($end)) {
            return $request->days_requested > 0 ? (float) $request->days_requested : 1.0;
        }

        $days = 0.0;
        $cursor = $start->copy();

        while ($cursor->lte($end)) {
            if (! $cursor->isWeekend()) {
                $days += 1;
            }

            $cursor->addDay();
        }

        return round($days, 2);
    }
}
