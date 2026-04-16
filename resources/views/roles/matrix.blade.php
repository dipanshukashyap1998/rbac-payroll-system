@extends('layouts.app', ['title' => 'Role Permission Matrix'])

@section('content')
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;">
            <h3 style="margin:0;">Role Permission Matrix</h3>
            <a class="btn btn-secondary" href="{{ route('roles.index') }}">Back to Roles</a>
        </div>

        <p style="margin-top:10px;color:#6b7280;">Toggle permissions per role and save once.</p>

        <form method="POST" action="{{ route('roles.matrix.sync') }}">
            @csrf

            <div style="overflow:auto; border:1px solid #e5e7eb; border-radius:10px; margin-top:12px;">
                <table class="table" style="margin-top:0; min-width:1000px;">
                    <thead>
                    <tr>
                        <th style="position:sticky;left:0;background:#fff;z-index:1;">Permission</th>
                        @foreach($roles as $role)
                            <th>{{ $role->name }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($permissions as $permission)
                        <tr>
                            <td style="position:sticky;left:0;background:#fff;z-index:1;">{{ $permission->name }}</td>
                            @foreach($roles as $role)
                                <td>
                                    <input
                                        type="checkbox"
                                        name="permissions[{{ $role->id }}][]"
                                        value="{{ $permission->id }}"
                                        @checked($role->permissions->contains('id', $permission->id))
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
@endsection
