@extends('layouts.app', ['title' => 'Roles'])

@section('content')
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900">Roles</h3>
            <div class="actions">
                <a class="btn btn-secondary" href="{{ route('roles.matrix') }}"><span class="material-symbols-rounded">grid_view</span>Permission Matrix</a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="table">
                <thead><tr><th>Name</th><th>Permissions</th><th>Assigned Users</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ $role->name }}</td>
                        <td>{{ $role->permissions_count }}</td>
                        <td>{{ $role->user_roles_count }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="{{ route('roles.edit', $role) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-slate-500">No roles found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $roles->links() }}</div>
    </div>
@endsection
