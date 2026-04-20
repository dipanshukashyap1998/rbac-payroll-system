@extends('layouts.app', ['title' => 'Create User'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">person_add</span> Identity setup</span>
                <h2>Create user</h2>
                <p>Create a new platform account and align it with the correct role and company scope.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('users.store') }}">
                @csrf

                <div class="form-grid split-grid">
                    <div class="field"><label>Name</label><input name="name" value="{{ old('name') }}" required></div>
                    <div class="field"><label>Email</label><input type="email" name="email" value="{{ old('email') }}" required></div>
                    <div class="field"><label>Password</label><input type="password" name="password" required></div>

                    <div class="field">
                        <label>Role</label>
                        <select name="role_id" required>
                            <option value="">Select role</option>
                            @foreach($roles as $role)
                                <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label>Company (required for employee)</label>
                        <select name="company_id">
                            <option value="">Select company</option>
                            @foreach($companies as $company)
                                <option value="{{ $company->id }}" @selected(old('company_id') == $company->id)>{{ $company->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field"><label>Designation (employee only)</label><input name="designation" value="{{ old('designation') }}"></div>
                    <div class="field"><label>Joining Date (employee only)</label><input type="date" name="joining_date" value="{{ old('joining_date') }}"></div>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Save User</button>
                    <a class="btn btn-secondary" href="{{ route('users.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
