@extends('layouts.app', ['title' => 'Edit Role'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">policy</span> Role maintenance</span>
                <h2>Edit role</h2>
                <p>Adjust role naming and refine the permission set without losing visibility.</p>
            </div>
        </section>

        <div class="card form-card">
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
                    <div class="rounded-2xl border border-slate-200 bg-slate-50/80 p-4">
                        <p class="mb-3 text-xs uppercase tracking-[0.18em] text-slate-500">Permission checklist</p>

                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach($permissions as $permission)
                                <label class="flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:border-teal-300 hover:bg-teal-50/40">
                                    <input
                                        type="checkbox"
                                        name="permission_ids[]"
                                        value="{{ $permission->id }}"
                                        @checked(in_array($permission->id, $selectedPermissions))
                                        class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
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
    </div>
@endsection
