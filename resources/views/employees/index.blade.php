@extends('layouts.app', ['title' => 'Employees'])

@section('content')
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900">Employee List</h3>
            @if(auth()->user()->isAdmin())
                <a class="btn btn-primary" href="{{ route('employees.create') }}"><span class="material-symbols-rounded">person_add</span>Create Employee</a>
            @endif
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
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
                        <td>{{ $employee->user?->name }}</td>
                        <td>{{ $employee->company?->name }}</td>
                        <td>{{ $employee->designation ?? '-' }}</td>
                        <td>
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $employee->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ ucfirst($employee->status) }}</span>
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
@endsection
