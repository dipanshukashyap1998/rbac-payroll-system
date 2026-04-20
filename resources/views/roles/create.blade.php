@extends('layouts.app', ['title' => 'Create Role'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">shield</span> Role design</span>
                <h2>Create role</h2>
                <p>Define a new role and attach the permission set it should own from day one.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="field">
                    <label>Role Name</label>
                    <input name="name" value="{{ old('name') }}" placeholder="example: finance_manager" required>
                </div>

                <div class="field">
                    <label>Assign Permissions</label>
                    <select name="permission_ids[]" multiple style="height:240px;">
                        @foreach($permissions as $permission)
                            <option value="{{ $permission->id }}" @selected(in_array($permission->id, old('permission_ids', [])))>{{ $permission->name }}</option>
                        @endforeach
                    </select>
                    <p class="help">Use multi-select to attach all relevant permissions for this role.</p>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Save Role</button>
                    <a class="btn btn-secondary" href="{{ route('roles.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
