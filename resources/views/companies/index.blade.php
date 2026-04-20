@extends('layouts.app', ['title' => 'Companies'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">apartment</span> Company workspace</span>
                <h2>Companies</h2>
                <p>Review company profiles, ownership, and current status from a cleaner operational table.</p>
            </div>
            <div class="hero-actions">
                @if(auth()->user()->isAdmin() && auth()->user()->hasPermission('company.create'))
                    <a class="btn btn-primary" href="{{ route('companies.create') }}"><span class="material-symbols-rounded">add</span>Create Company</a>
                @endif
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">Company list</h3>
                    <p class="panel-copy">Each row summarizes onboarding and company account status.</p>
                </div>
            </div>

            <div class="table-wrap">
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
                        <td>
                            <div class="font-medium text-slate-900">{{ $company->name }}</div>
                            <div class="text-sm text-slate-500">{{ $company->city ?: 'Location pending' }}</div>
                        </td>
                        <td>{{ $company->email ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $company->status === 'active' ? 'badge-success' : ($company->status === 'suspended' ? 'badge-warning' : 'badge-muted') }}">{{ ucfirst($company->status) }}</span>
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
    </div>
@endsection
