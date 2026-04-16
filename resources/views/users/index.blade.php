@extends('layouts.app', ['title' => 'Users'])

@section('content')
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900">Users</h3>
            <a class="btn btn-primary" href="{{ route('users.create') }}"><span class="material-symbols-rounded">person_add</span>Create User</a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Company</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->roles->pluck('name')->implode(', ') ?: '-' }}</td>
                        <td>{{ $user->employeeProfile?->company?->name ?? $user->userRoles->first()?->company?->name ?? '-' }}</td>
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
@endsection
