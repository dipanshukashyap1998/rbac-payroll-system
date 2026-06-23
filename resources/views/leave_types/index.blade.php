@extends('layouts.app', ['title' => 'Leave Types'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">event_available</span> Leave policy</span>
                <h2>Company leave types</h2>
                <p>Configure the leave categories your company offers, including annual allotment, carry-forward rules, and whether approval is required.</p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('leave-types.create') }}" class="btn btn-primary"><span class="material-symbols-rounded">add</span>New leave type</a>
                <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary"><span class="material-symbols-rounded">fact_check</span>Leave requests</a>
            </div>
        </section>

        <div class="card">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Code</th>
                        <th>Annual allotment</th>
                        <th>Paid</th>
                        <th>Carry forward</th>
                        <th>Approval</th>
                        <th>Balances</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($leaveTypes as $leaveType)
                        <tr>
                            <td>{{ $leaveType->name }}</td>
                            <td>{{ $leaveType->code }}</td>
                            <td>{{ number_format($leaveType->default_days, 2) }}</td>
                            <td>{{ $leaveType->is_paid ? 'Paid' : 'Unpaid' }}</td>
                            <td>{{ $leaveType->carry_forward ? 'Yes' : 'No' }}</td>
                            <td>{{ $leaveType->requires_approval ? 'Required' : 'Auto-approved' }}</td>
                            <td>{{ $leaveType->balances_count }}</td>
                            <td class="actions">
                                <a class="btn btn-secondary" href="{{ route('leave-types.edit', $leaveType) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                                <form method="POST" action="{{ route('leave-types.destroy', $leaveType) }}" onsubmit="return confirm('Delete this leave type?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit"><span class="material-symbols-rounded">delete</span>Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-slate-500">No leave types configured yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
