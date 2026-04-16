@extends('layouts.app', ['title' => 'Edit User'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="field"><label>Name</label><input name="name" value="{{ old('name', $user->name) }}" required></div>
            <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email', $user->email) }}" required></div>
            <div class="field"><label>Password (leave empty to keep current)</label><input type="password" name="password"></div>

            <div class="field">
                <label>Role</label>
                <select name="role_id" required>
                    <option value="">Select role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" @selected((int) old('role_id', $selectedRole?->id) === (int) $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field">
                <label>Company (required for employee)</label>
                <select name="company_id">
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}" @selected((int) old('company_id', $selectedCompanyId) === (int) $company->id)>{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="field"><label>Designation (employee only)</label><input name="designation" value="{{ old('designation', $user->employeeProfile?->designation) }}"></div>
            <div class="field"><label>Joining Date (employee only)</label><input type="date" name="joining_date" value="{{ old('joining_date', optional($user->employeeProfile?->joining_date)->format('Y-m-d')) }}"></div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Update User</button>
                <a class="btn btn-secondary" href="{{ route('users.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
