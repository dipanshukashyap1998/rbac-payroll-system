@extends('layouts.app', ['title' => 'Employees'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">groups</span> Workforce</span>
                <h2>Employees</h2>
                <p>Manage employee assignments, their designations, and current employment status.</p>
            </div>
            <div class="hero-actions">
                @if(auth()->user()->isAdmin())
                    <a class="btn btn-primary" href="{{ route('employees.create') }}"><span class="material-symbols-rounded">person_add</span>Create Employee</a>
                @endif
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">Employee list</h3>
                    <p class="panel-copy">A compact roster of assigned users inside your company scope.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                <thead>
                <tr>
                    <th>User</th>
                    <th>Company</th>
                    <th>Designation</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>
                            <div class="font-medium text-slate-900">{{ $employee->user?->name }}</div>
                            <div class="text-sm text-slate-500">{{ $employee->user?->email }}</div>
                        </td>
                        <td>{{ $employee->company?->name }}</td>
                        <td>{{ $employee->designation ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $employee->status === 'active' ? 'badge-success' : 'badge-muted' }}">{{ ucfirst($employee->status) }}</span>
                        </td>
                        <td>
                            @if(auth()->user()->isAdmin())
                                <div class="actions">
                                    <a class="btn btn-secondary" href="{{ route('employees.edit', $employee) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                                    <form method="POST" action="{{ route('employees.destroy', $employee) }}" onsubmit="return confirm('Delete this employee?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit"><span class="material-symbols-rounded">delete</span>Delete</button>
                                    </form>
                                </div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-slate-500">No employees found.</td></tr>
                @endforelse
                </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $employees->links() }}</div>
        </div>
    </div>
@endsection
