@extends('layouts.app', ['title' => 'Edit Role'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')
            <div class="field">
                <label>Role Name</label>
                <input name="name" value="{{ old('name', $role->name) }}" required>
            </div>

            <div class="field">
                <label>Assign Permissions</label>
                @php($selectedPermissions = old('permission_ids', $role->permissions()->pluck('permissions.id')->all()))
                <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                    <p class="mb-3 text-xs text-slate-500">Select one or more permissions for this role.</p>

                    <div class="grid gap-2 sm:grid-cols-2">
                        @foreach($permissions as $permission)
                            <label class="flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:border-blue-300 hover:bg-blue-50/40">
                                <input
                                    type="checkbox"
                                    name="permission_ids[]"
                                    value="{{ $permission->id }}"
                                    @checked(in_array($permission->id, $selectedPermissions))
                                    class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500"
                                >
                                <span>{{ $permission->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Update Role</button>
                <a class="btn btn-secondary" href="{{ route('roles.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
