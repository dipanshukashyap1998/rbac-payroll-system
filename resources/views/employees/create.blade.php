@extends('layouts.app', ['title' => 'Create Employee'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">person_add</span> Workforce setup</span>
                <h2>Create employee</h2>
                <p>Assign an existing user to the employee roster and set the essential employment metadata.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('employees.store') }}">
                @csrf

                <div class="form-grid split-grid">
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
    </div>
@endsection
