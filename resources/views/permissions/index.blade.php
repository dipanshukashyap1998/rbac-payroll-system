@extends('layouts.app', ['title' => 'Permissions'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">key</span> Fine-grained access</span>
                <h2>Permissions</h2>
                <p>Control the exact actions each role can perform across the product.</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-primary" href="{{ route('permissions.create') }}"><span class="material-symbols-rounded">key</span>Create Permission</a>
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">Permission registry</h3>
                    <p class="panel-copy">Every permission stays visible with role usage side by side.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                <thead><tr><th>Name</th><th>Roles</th><th>Actions</th></tr></thead>
                <tbody>
                @forelse($permissions as $permission)
                    <tr>
                        <td>
                            <div class="font-medium text-slate-900">{{ $permission->name }}</div>
                            <div class="text-sm text-slate-500">{{ str($permission->name)->before('.')->replace('_', ' ')->title() }}</div>
                        </td>
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
    </div>
@endsection
