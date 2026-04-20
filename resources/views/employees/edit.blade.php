@extends('layouts.app', ['title' => 'Edit Employee'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">manage_accounts</span> Workforce maintenance</span>
                <h2>Edit employee</h2>
                <p>Refine employee details without leaving the operational flow.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('employees.update', $employee) }}">
                @csrf
                @method('PUT')

                <div class="form-grid split-grid">
                    <div class="field">
                        <label>User</label>
                        <select name="user_id" required>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('user_id', $employee->user_id) == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Employee Code</label>
                        <input name="employee_code" value="{{ old('employee_code', $employee->employee_code) }}">
                    </div>

                    <div class="field">
                        <label>Designation</label>
                        <input name="designation" value="{{ old('designation', $employee->designation) }}">
                    </div>

                    <div class="field">
                        <label>Joining Date</label>
                        <input type="date" name="joining_date" value="{{ old('joining_date', optional($employee->joining_date)->format('Y-m-d')) }}">
                    </div>
                </div>

                <div class="field">
                    <label>Status</label>
                    <select name="status">
                        @foreach(['active','inactive'] as $status)
                            <option value="{{ $status }}" @selected(old('status', $employee->status) === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a class="btn btn-secondary" href="{{ route('employees.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
