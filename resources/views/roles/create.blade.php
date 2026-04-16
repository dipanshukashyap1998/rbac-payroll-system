@extends('layouts.app', ['title' => 'Create Role'])

@section('content')
    <div class="card" style="max-width:700px;">
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
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Save Role</button>
                <a class="btn btn-secondary" href="{{ route('roles.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
