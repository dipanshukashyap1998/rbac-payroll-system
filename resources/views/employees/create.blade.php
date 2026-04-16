@extends('layouts.app', ['title' => 'Create Employee'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('employees.store') }}">
            @csrf

            <div class="field">
                <label>User</label>
                <select name="user_id" required>
                    <option value="">Select user</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id') == $user->id)>{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Employee Code</label>
                <input name="employee_code" value="{{ old('employee_code') }}">
            </div>

            <div class="field">
                <label>Designation</label>
                <input name="designation" value="{{ old('designation') }}">
            </div>

            <div class="field">
                <label>Joining Date</label>
                <input type="date" name="joining_date" value="{{ old('joining_date') }}">
            </div>

            <div class="field">
                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-secondary" href="{{ route('employees.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
