@extends('layouts.app', ['title' => 'Salary Structures'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">account_balance_wallet</span> Compensation design</span>
                <h2>Salary structures</h2>
                <p>Organize salary templates and keep compensation components easy to compare.</p>
            </div>
            <div class="hero-actions">
                @if(auth()->user()->isAdmin())
                    <a class="btn btn-primary" href="{{ route('salary-structures.create') }}"><span class="material-symbols-rounded">add</span>Create Structure</a>
                @endif
            </div>
        </section>

        <div class="card">
            <div class="toolbar-card mb-4">
                <div>
                    <h3 class="panel-title">Structure list</h3>
                    <p class="panel-copy">Base salary and component values are surfaced in one modern table.</p>
                </div>
            </div>

            <div class="table-wrap">
                <table class="table">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Company</th>
                    <th>Base Salary</th>
                    <th>HRA</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($salaryStructures as $salaryStructure)
                    <tr>
                        <td>
                            <div class="font-medium text-slate-900">{{ $salaryStructure->name ?? '-' }}</div>
                            <div class="text-sm text-slate-500">Template</div>
                        </td>
                        <td>{{ $salaryStructure->company?->name }}</td>
                        <td>{{ number_format($salaryStructure->base_salary, 2) }}</td>
                        <td>{{ number_format($salaryStructure->hra, 2) }}</td>
                        <td>
                            @if(auth()->user()->isAdmin())
                                <div class="actions">
                                    <a class="btn btn-secondary" href="{{ route('salary-structures.edit', $salaryStructure) }}"><span class="material-symbols-rounded">edit</span>Edit</a>
                                    <form method="POST" action="{{ route('salary-structures.destroy', $salaryStructure) }}" onsubmit="return confirm('Delete this salary structure?')">
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
                    <tr><td colspan="5" class="text-slate-500">No salary structures found.</td></tr>
                @endforelse
                </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $salaryStructures->links() }}</div>
        </div>
    </div>
@endsection
