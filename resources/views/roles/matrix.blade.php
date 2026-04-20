@extends('layouts.app', ['title' => 'Role Permission Matrix'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">grid_view</span> Access matrix</span>
                <h2>Role permission matrix</h2>
                <p>Toggle role capabilities in one place, then persist the entire policy set in a single save.</p>
            </div>
            <div class="hero-actions">
                <a class="btn btn-secondary" href="{{ route('roles.index') }}">Back to Roles</a>
            </div>
        </section>

        <div class="card">
            <form method="POST" action="{{ route('roles.matrix.sync') }}">
                @csrf

                <div class="table-wrap">
                    <table class="table" style="min-width:1000px;">
                        <thead>
                        <tr>
                            <th style="position:sticky;left:0;background:#f8fafc;z-index:1;">Permission</th>
                            @foreach($roles as $role)
                                <th>{{ $role->name }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($permissions as $permission)
                            <tr>
                                <td style="position:sticky;left:0;background:white;z-index:1;">
                                    <div class="font-medium text-slate-900">{{ $permission->name }}</div>
                                </td>
                                @foreach($roles as $role)
                                    <td>
                                        <input
                                            type="checkbox"
                                            name="permissions[{{ $role->id }}][]"
                                            value="{{ $permission->id }}"
                                            @checked($role->permissions->contains('id', $permission->id))
                                            class="h-4 w-4 rounded border-slate-300 text-teal-600 focus:ring-teal-500"
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="actions" style="margin-top:14px;">
                    <button class="btn btn-primary" type="submit">Save Matrix</button>
                </div>
            </form>
        </div>
    </div>
@endsection
