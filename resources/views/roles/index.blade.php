@extends('layouts.app', ['title' => 'Roles'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">security</span> Access control</span>
                <h2>Roles</h2>
                <p>Keep your role model tidy, measurable, and easy to audit.</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-secondary" href="{{ route('roles.matrix') }}"><span class="material-symbols-rounded">grid_view</span>Permission Matrix</a>
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">Role library</h3>
                    <p class="panel-copy">Review how many permissions and users are connected to each role.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                <thead><tr><th>Name</th><th>Permissions</th><th>Assigned Users</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>
                            <div class="font-medium text-slate-900">{{ str($role->name)->replace('_', ' ')->title() }}</div>
                            <div class="text-sm text-slate-500">{{ $role->name }}</div>
                        </td>
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
    </div>
@endsection
