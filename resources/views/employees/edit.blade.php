@extends('layouts.app', ['title' => 'Edit Employee'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('employees.update', $employee) }}">
            @csrf
            @method('PUT')

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
@endsection
