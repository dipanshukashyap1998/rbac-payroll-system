@extends('layouts.app', ['title' => 'Apply Leave'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">event_note</span> Leave application</span>
                <h2>Apply for leave</h2>
                <p>Choose the leave type, dates, and reason. Your balance is shown in advance so you can avoid overshooting the quota.</p>
            </div>
            <div class="hero-actions">
                <a href="{{ route('leave-requests.index', ['month' => request('month')]) }}" class="btn btn-secondary">Back to calendar</a>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('leave-requests.store') }}" class="form-grid">
                @csrf
                <div class="field">
                    <label>Leave type</label>
                    <select name="leave_type_id">
                        <option value="">Select leave type</option>
                        @foreach($leaveTypes as $leaveType)
                            @php($balance = $balances->get($leaveType->id))
                            @php($available = $balance ? (($balance->opening_balance + $balance->allocated_days) - ($balance->used_days + $balance->pending_days + $balance->encashed_days)) : $leaveType->default_days)
                            <option value="{{ $leaveType->id }}" @selected(old('leave_type_id') == $leaveType->id)>
                                {{ $leaveType->name }} (available: {{ number_format($available, 2) }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="split-grid">
                    <div class="field"><label>Start date</label><input type="date" name="start_date" value="{{ old('start_date', request('start_date')) }}"></div>
                    <div class="field"><label>End date</label><input type="date" name="end_date" value="{{ old('end_date', request('end_date', request('start_date'))) }}"></div>
                </div>

                <div class="split-grid">
                    <div class="field">
                        <label>Start session</label>
                        <select name="session_start">
                            <option value="full_day" @selected(old('session_start', 'full_day') === 'full_day')>Full day</option>
                            <option value="first_half" @selected(old('session_start') === 'first_half')>First half</option>
                            <option value="second_half" @selected(old('session_start') === 'second_half')>Second half</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>End session</label>
                        <select name="session_end">
                            <option value="full_day" @selected(old('session_end', 'full_day') === 'full_day')>Full day</option>
                            <option value="first_half" @selected(old('session_end') === 'first_half')>First half</option>
                            <option value="second_half" @selected(old('session_end') === 'second_half')>Second half</option>
                        </select>
                    </div>
                </div>

                <div class="field">
                    <label>Reason</label>
                    <textarea name="reason" rows="4">{{ old('reason') }}</textarea>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Submit request</button>
                    <a class="btn btn-secondary" href="{{ route('leave-requests.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
