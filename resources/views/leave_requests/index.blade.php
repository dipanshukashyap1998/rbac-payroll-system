@extends('layouts.app', ['title' => 'Leave Requests'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">event_note</span> Leave module</span>
                <h2>{{ $isEmployeeView ? 'My leave requests' : 'Company leave requests' }}</h2>
                <p>{{ $isEmployeeView ? 'Check your available balances and request history.' : 'Review employee leave requests, balances, and approvals.' }}</p>
            </div>

            <div class="hero-actions">
                @if($isEmployeeView)
                    <a href="{{ route('leave-requests.create') }}" class="btn btn-primary"><span class="material-symbols-rounded">add</span>Apply leave</a>
                @else
                    <a href="{{ route('leave-types.index') }}" class="btn btn-secondary"><span class="material-symbols-rounded">event_available</span>Leave types</a>
                @endif
            </div>
        </section>

        @if($isEmployeeView)
            <div class="card">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="panel-title">Leave calendar</h3>
                        <p class="panel-copy">See approved and pending leave entries for the current calendar month and jump straight into a leave request.</p>
                    </div>
                    <div class="actions">
                        <a class="btn btn-secondary" href="{{ route('leave-requests.index', ['month' => $leaveCalendar['previous_month']]) }}">Previous</a>
                        <a class="btn btn-secondary" href="{{ route('leave-requests.index', ['month' => $leaveCalendar['next_month']]) }}">Next</a>
                        <a class="btn btn-primary" href="{{ route('leave-requests.create', ['month' => $leaveCalendar['month_start']]) }}">Apply leave</a>
                    </div>
                </div>

                <div class="mt-4 grid grid-cols-7 gap-2 text-center text-xs font-semibold uppercase tracking-wide text-slate-400">
                    <div>Sun</div><div>Mon</div><div>Tue</div><div>Wed</div><div>Thu</div><div>Fri</div><div>Sat</div>
                </div>

                <div class="mt-2 grid grid-cols-7 gap-2">
                    @foreach($leaveCalendar['days'] as $day)
                        <a
                            href="{{ route('leave-requests.create', ['month' => $leaveCalendar['month_start'], 'start_date' => $day['date']->toDateString(), 'end_date' => $day['date']->toDateString()]) }}"
                            class="min-h-[108px] rounded-2xl border p-3 text-left transition {{ $day['is_current_month'] ? 'border-white/10 bg-slate-50/70' : 'border-dashed border-slate-300 bg-slate-100/60 opacity-55' }} {{ $day['is_today'] ? 'ring-2 ring-brand-500' : '' }}"
                        >
                            <div class="flex items-start justify-between gap-2">
                                <div class="text-sm font-semibold text-slate-900">{{ $day['date']->format('j') }}</div>
                                @if($day['is_weekend'])
                                    <span class="badge badge-muted">Off</span>
                                @endif
                            </div>

                            <div class="mt-2 space-y-1">
                                @forelse($day['leave_items'] as $leaveItem)
                                    <div class="rounded-lg px-2 py-1 text-xs font-medium {{ $leaveItem['status'] === 'approved' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                        {{ $leaveItem['type'] }} · {{ ucfirst($leaveItem['status']) }}
                                    </div>
                                @empty
                                    <div class="text-xs text-slate-500">Click to request leave</div>
                                @endforelse
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="card">
                <h3 class="panel-title">My leave balances - {{ $leaveYear }}</h3>
                <div class="table-wrap mt-4">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Leave type</th>
                            <th>Allotted</th>
                            <th>Used</th>
                            <th>Pending</th>
                            <th>Available</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($leaveTypes as $leaveType)
                            @php($balance = $balances->get($leaveType->id))
                            @php($available = $balance ? (($balance->opening_balance + $balance->allocated_days) - ($balance->used_days + $balance->pending_days + $balance->encashed_days)) : $leaveType->default_days)
                            <tr>
                                <td>{{ $leaveType->name }}</td>
                                <td>{{ number_format($balance->allocated_days ?? $leaveType->default_days, 2) }}</td>
                                <td>{{ number_format($balance->used_days ?? 0, 2) }}</td>
                                <td>{{ number_format($balance->pending_days ?? 0, 2) }}</td>
                                <td>{{ number_format($available, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-slate-500">No leave types configured for your company yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

        <div class="card">
            <h3 class="panel-title">Leave requests</h3>
            <div class="table-wrap mt-4">
                <table class="table">
                    <thead>
                    <tr>
                        @if(! $isEmployeeView)
                            <th>Employee</th>
                        @endif
                        <th>Type</th>
                        <th>Dates</th>
                        <th>Days</th>
                        <th>Status</th>
                        <th>Reason</th>
                        @if(! $isEmployeeView)
                            <th>Action</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($leaveRequests as $leaveRequest)
                        <tr>
                            @if(! $isEmployeeView)
                                <td>{{ $leaveRequest->employee?->user?->name ?? '-' }}</td>
                            @endif
                            <td>{{ $leaveRequest->leaveType?->name ?? '-' }}</td>
                            <td>{{ optional($leaveRequest->start_date)->format('d M Y') }} - {{ optional($leaveRequest->end_date)->format('d M Y') }}</td>
                            <td>{{ number_format($leaveRequest->days_requested, 2) }}</td>
                            <td><span class="badge {{ $leaveRequest->status === 'approved' ? 'badge-success' : ($leaveRequest->status === 'rejected' ? 'badge-muted' : 'badge-warning') }}">{{ ucfirst($leaveRequest->status) }}</span></td>
                            <td>{{ $leaveRequest->reason ?? '-' }}</td>
                            @if(! $isEmployeeView)
                                <td class="actions">
                                    @if($leaveRequest->status === 'pending')
                                        <form method="POST" action="{{ route('leave-requests.approve', $leaveRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button class="btn btn-primary" type="submit">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('leave-requests.reject', $leaveRequest) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="rejection_reason" value="Rejected by company">
                                            <button class="btn btn-danger" type="submit">Reject</button>
                                        </form>
                                    @else
                                        <span class="text-slate-500">No action</span>
                                    @endif
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr><td colspan="{{ $isEmployeeView ? 6 : 7 }}" class="text-slate-500">No leave requests found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(! $isEmployeeView)
                <div class="mt-4">{{ $leaveRequests->links() }}</div>
            @endif
        </div>
    </div>
@endsection
