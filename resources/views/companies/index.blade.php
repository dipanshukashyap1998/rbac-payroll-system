@extends('layouts.app', ['title' => 'Companies'])

@section('content')
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900">Company List</h3>
            @if(auth()->user()->isAdmin() && auth()->user()->hasPermission('company.create'))
                <a class="btn btn-primary" href="{{ route('companies.create') }}"><span class="material-symbols-rounded">add</span>Create Company</a>
            @endif
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
            <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>{{ $company->name }}</td>
                        <td>{{ $company->email ?? '-' }}</td>
                        <td>
                            <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $company->status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">{{ ucfirst($company->status) }}</span>
                        </td>
                        <td>
                            <div class="actions">
                                @if(auth()->user()->isAdmin())
                                    <a class="btn btn-secondary" href="{{ route('companies.edit', $company) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                                @endif

                                @if(auth()->user()->isSuperAdmin())
                                    <form method="POST" action="{{ route('companies.destroy', $company) }}" onsubmit="return confirm('Delete this company?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger" type="submit"><span class="material-symbols-rounded">delete</span>Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-slate-500">No companies found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $companies->links() }}</div>
    </div>
@endsection
