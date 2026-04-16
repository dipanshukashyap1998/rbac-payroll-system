@extends('layouts.app', ['title' => 'Permissions'])

@section('content')
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900">Permissions</h3>
            <a class="btn btn-primary" href="{{ route('permissions.create') }}"><span class="material-symbols-rounded">key</span>Create Permission</a>
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="table">
                <thead><tr><th>Name</th><th>Roles</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td>{{ $permission->name }}</td>
                        <td>{{ $permission->roles_count }}</td>
                        <td>
                            <div class="actions">
                                <a class="btn btn-secondary" href="{{ route('permissions.edit', $permission) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                                <form method="POST" action="{{ route('permissions.destroy', $permission) }}" onsubmit="return confirm('Delete this permission?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger" type="submit"><span class="material-symbols-rounded">delete</span>Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="3" class="text-slate-500">No permissions found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $permissions->links() }}</div>
    </div>
@endsection
