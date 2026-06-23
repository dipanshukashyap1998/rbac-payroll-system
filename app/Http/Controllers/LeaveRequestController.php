<?php

namespace App\Http\Controllers;

use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Services\LeavePolicyService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LeaveRequestController extends Controller
{
    public function __construct(private LeavePolicyService $leavePolicyService)
    {
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        if ($user->isEmployee()) {
            $employee = $user->employeeProfile;
            $leaveYear = (int) now()->year;
            $monthStart = $this->resolveCalendarMonth($request->query('month'));

            $leaveTypes = LeaveType::query()
                ->where('company_id', $employee?->company_id)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            $balances = LeaveBalance::query()
                ->with('leaveType')
                ->where('employee_id', $employee?->id)
                ->where('leave_year', $leaveYear)
                ->get()
                ->keyBy('leave_type_id');

            $leaveRequests = LeaveRequest::query()
                ->with('leaveType')
                ->where('employee_id', $employee?->id)
                ->latest()
                ->get();

            $leaveCalendar = $this->buildLeaveCalendar($employee, $monthStart);

            return view('leave_requests.index', [
                'leaveRequests' => $leaveRequests,
                'leaveTypes' => $leaveTypes,
                'balances' => $balances,
                'isEmployeeView' => true,
                'leaveYear' => $leaveYear,
                'leaveCalendar' => $leaveCalendar,
            ]);
        }

        $companyId = $user->ownedCompany()->value('id');
        $leaveRequests = LeaveRequest::query()
            ->with(['employee.user', 'leaveType', 'approver'])
            ->where('company_id', $companyId)
            ->latest()
            ->paginate(15);

        $leaveTypes = LeaveType::query()
            ->where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        return view('leave_requests.index', [
            'leaveRequests' => $leaveRequests,
            'leaveTypes' => $leaveTypes,
            'balances' => collect(),
            'isEmployeeView' => false,
            'leaveYear' => (int) now()->year,
        ]);
    }

    public function create(Request $request): View
    {
        $user = $request->user();
        abort_unless($user->isEmployee(), 403, 'Only employees can request leave.');

        $employee = $user->employeeProfile;
        $monthStart = $this->resolveCalendarMonth($request->query('month'));
        $leaveTypes = LeaveType::query()
            ->where('company_id', $employee?->company_id)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $leaveYear = (int) now()->year;
        $balances = LeaveBalance::query()
            ->with('leaveType')
            ->where('employee_id', $employee?->id)
            ->where('leave_year', $leaveYear)
            ->get()
            ->keyBy('leave_type_id');

        $leaveCalendar = $this->buildLeaveCalendar($employee, $monthStart);

        return view('leave_requests.create', compact('leaveTypes', 'balances', 'leaveYear', 'leaveCalendar'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user->isEmployee(), 403, 'Only employees can request leave.');

        $employee = $user->employeeProfile;
        $companyId = $employee?->company_id;

        $data = $request->validate([
            'leave_type_id' => ['required', 'exists:leave_types,id'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'session_start' => ['nullable', 'in:full_day,first_half,second_half'],
            'session_end' => ['nullable', 'in:full_day,first_half,second_half'],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $leaveType = LeaveType::query()
            ->where('id', $data['leave_type_id'])
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->firstOrFail();

        $startDate = Carbon::parse($data['start_date']);
        $endDate = Carbon::parse($data['end_date']);
        $daysRequested = $this->leavePolicyService->calculateRequestedDays(
            $startDate,
            $endDate,
            $data['session_start'] ?? null,
            $data['session_end'] ?? null
        );

        $leaveYear = (int) $startDate->year;
        $this->leavePolicyService->ensureEmployeeBalances($employee, $leaveYear);

        $balance = LeaveBalance::query()
            ->where('employee_id', $employee->id)
            ->where('leave_type_id', $leaveType->id)
            ->where('leave_year', $leaveYear)
            ->firstOrFail();

        $available = $this->leavePolicyService->availableBalance($employee, $leaveType, $leaveYear);

        if ($leaveType->is_paid && $daysRequested > $available) {
            return back()->withErrors([
                'leave_type_id' => "Only {$available} day(s) are available for this leave type.",
            ])->withInput();
        }

        return DB::transaction(function () use ($employee, $companyId, $leaveType, $data, $startDate, $endDate, $daysRequested, $balance) {
            $status = $leaveType->requires_approval ? 'pending' : 'approved';

            $leaveRequest = LeaveRequest::query()->create([
                'employee_id' => $employee->id,
                'leave_type_id' => $leaveType->id,
                'company_id' => $companyId,
                'start_date' => $startDate->toDateString(),
                'end_date' => $endDate->toDateString(),
                'days_requested' => $daysRequested,
                'session_start' => $data['session_start'] ?? null,
                'session_end' => $data['session_end'] ?? null,
                'reason' => $data['reason'] ?? null,
                'status' => $status,
            ]);

            if ($status === 'pending') {
                $balance->increment('pending_days', $daysRequested);
            } else {
                $balance->increment('used_days', $daysRequested);
            }

            return redirect()
                ->route('leave-requests.index')
                ->with('status', $leaveRequest->status === 'approved' ? 'Leave approved and recorded.' : 'Leave request submitted for approval.');
        });
    }

    public function approve(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorizeCompanyAccess($request, $leaveRequest);

        abort_unless($leaveRequest->status === 'pending', 422, 'Only pending requests can be approved.');

        $balance = LeaveBalance::query()
            ->where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('leave_year', Carbon::parse($leaveRequest->start_date)->year)
            ->firstOrFail();

        return DB::transaction(function () use ($request, $leaveRequest, $balance) {
            $balance->decrement('pending_days', $leaveRequest->days_requested);
            $balance->increment('used_days', $leaveRequest->days_requested);

            $leaveRequest->update([
                'status' => 'approved',
                'approved_by' => $request->user()->id,
                'decision_at' => now(),
                'rejection_reason' => null,
            ]);

            return back()->with('status', 'Leave request approved.');
        });
    }

    public function reject(Request $request, LeaveRequest $leaveRequest): RedirectResponse
    {
        $this->authorizeCompanyAccess($request, $leaveRequest);

        abort_unless($leaveRequest->status === 'pending', 422, 'Only pending requests can be rejected.');

        $data = $request->validate([
            'rejection_reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $balance = LeaveBalance::query()
            ->where('employee_id', $leaveRequest->employee_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('leave_year', Carbon::parse($leaveRequest->start_date)->year)
            ->firstOrFail();

        return DB::transaction(function () use ($request, $leaveRequest, $balance, $data) {
            $balance->decrement('pending_days', $leaveRequest->days_requested);

            $leaveRequest->update([
                'status' => 'rejected',
                'approved_by' => $request->user()->id,
                'decision_at' => now(),
                'rejection_reason' => $data['rejection_reason'] ?? null,
            ]);

            return back()->with('status', 'Leave request rejected.');
        });
    }

    private function authorizeCompanyAccess(Request $request, LeaveRequest $leaveRequest): void
    {
        $companyId = $request->user()->ownedCompany()->value('id');
        abort_unless((int) $leaveRequest->company_id === (int) $companyId, 403, 'You cannot manage this leave request.');
    }

    private function resolveCalendarMonth(mixed $monthInput): Carbon
    {
        if (! $monthInput) {
            return now()->startOfMonth();
        }

        try {
            return Carbon::parse($monthInput)->startOfMonth();
        } catch (\Throwable) {
            return now()->startOfMonth();
        }
    }

    private function buildLeaveCalendar($employee, Carbon $monthStart): array
    {
        $monthEnd = $monthStart->copy()->endOfMonth();
        $periodStart = $monthStart->copy()->startOfWeek(Carbon::SUNDAY);
        $periodEnd = $monthEnd->copy()->endOfWeek(Carbon::SATURDAY);

        $requests = LeaveRequest::query()
            ->with('leaveType')
            ->where('employee_id', $employee?->id)
            ->whereIn('status', ['approved', 'pending'])
            ->whereDate('start_date', '<=', $periodEnd->toDateString())
            ->whereDate('end_date', '>=', $periodStart->toDateString())
            ->get();

        $days = [];
        $cursor = $periodStart->copy();

        while ($cursor->lte($periodEnd)) {
            $matches = $requests->filter(function (LeaveRequest $request) use ($cursor) {
                $start = Carbon::parse($request->start_date)->startOfDay();
                $end = Carbon::parse($request->end_date)->startOfDay();

                return $cursor->betweenIncluded($start, $end);
            })->values();

            $days[] = [
                'date' => $cursor->copy(),
                'is_current_month' => $cursor->month === $monthStart->month,
                'is_today' => $cursor->isToday(),
                'is_weekend' => $cursor->isWeekend(),
                'leave_items' => $matches->map(function (LeaveRequest $request) {
                    return [
                        'type' => $request->leaveType?->name ?? 'Leave',
                        'status' => $request->status,
                    ];
                })->all(),
            ];

            $cursor->addDay();
        }

        return [
            'month' => $monthStart->format('F Y'),
            'month_start' => $monthStart->toDateString(),
            'previous_month' => $monthStart->copy()->subMonthNoOverflow()->toDateString(),
            'next_month' => $monthStart->copy()->addMonthNoOverflow()->toDateString(),
            'days' => $days,
        ];
    }
}
