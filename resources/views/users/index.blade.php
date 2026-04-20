@extends('layouts.app', ['title' => 'Users'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">group</span> Identity management</span>
                <h2>Users</h2>
                <p>Inspect platform-wide account details, role assignments, and company relationships in one place.</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-primary" href="{{ route('users.create') }}"><span class="material-symbols-rounded">person_add</span>Create User</a>
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">User directory</h3>
                    <p class="panel-copy">A broader account view for superadmin oversight and quick maintenance.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                <thead><tr><th>User</th><th>Access</th><th>Company</th><th>Status</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($users as $user)
                    @php($primaryCompany = $user->ownedCompany ?? $user->employeeProfile?->company ?? $user->userRoles->first()?->company)
                    <tr>
                        <td>
                            <div class="font-medium text-slate-900">{{ $user->name }}</div>
                            <div class="text-sm text-slate-500">{{ $user->email }}</div>
                        </td>
                        <td>
                            <div class="text-sm font-medium text-slate-700">{{ $user->roles->pluck('name')->map(fn ($role) => str($role)->replace('_', ' ')->title())->implode(', ') ?: '-' }}</div>
                            <div class="text-xs text-slate-500">
                                @if($user->isSuperAdmin())
                                    Global access
                                @elseif($user->isAdmin())
                                    Company owner access
                                @elseif($user->isEmployee())
                                    Employee access
                                @else
                                    Standard access
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="font-medium text-slate-900">{{ $primaryCompany?->name ?? 'No company assigned' }}</div>
                            <div class="text-xs text-slate-500">
                                @if($user->isAdmin())
                                    Owned by this admin
                                @elseif($primaryCompany)
                                    {{ ucfirst($primaryCompany->status) }}
                                @else
                                    Pending company setup
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium {{ $user->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="{{ route('users.edit', $user) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                                <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Delete this user?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit"><span class="material-symbols-rounded">delete</span>Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-slate-500">No users found.</td></tr>
                @endforelse
                </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
@endsection
