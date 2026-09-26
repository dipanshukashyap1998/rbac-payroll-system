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
                    <div class="space-y-5">
                        @foreach($permissionGroups as $groupName => $groupPermissions)
                            <section class="border-b border-slate-700/50 pb-5 last:border-0 last:pb-0" aria-labelledby="permission-group-{{ $loop->index }}">
                                <div class="mb-3 flex items-center justify-between gap-3">
                                    <h3 id="permission-group-{{ $loop->index }}" class="text-base font-semibold text-slate-100">{{ $groupName }}</h3>
                                    <span class="text-sm text-slate-400">{{ $groupPermissions->count() }} {{ str('permission')->plural($groupPermissions->count()) }}</span>
                                </div>

                                <div class="grid gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                    @foreach($groupPermissions as $permission)
                                        @php($permissionLabel = str($permission->name)->contains('.') ? str($permission->name)->after('.')->replace(['.', '_'], ' ')->title() : str($permission->name)->replace(['.', '_'], ' ')->title())
                                        <label class="flex min-w-0 items-center gap-3 rounded-lg border border-slate-700 bg-slate-900/50 px-3 py-2.5 text-sm text-slate-200 transition hover:border-teal-500/70 hover:bg-slate-800/70">
                                            <input
                                                type="checkbox"
                                                name="permission_ids[]"
                                                value="{{ $permission->id }}"
                                                @checked(in_array($permission->id, $selectedPermissions))
                                                class="h-4 w-4 shrink-0 rounded border-slate-500 text-teal-500 focus:ring-teal-400"
                                            >
                                            <span class="break-words">{{ $permissionLabel }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </section>
                            @endforeach
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
