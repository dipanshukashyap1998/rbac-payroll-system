@extends('layouts.app', ['title' => 'Salary Structures'])

@section('content')
    <div class="card">
        <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
            <h3 class="text-lg font-semibold text-slate-900">Salary Structure List</h3>
            @if(auth()->user()->isAdmin())
                <a class="btn btn-primary" href="{{ route('salary-structures.create') }}"><span class="material-symbols-rounded">add</span>Create Structure</a>
            @endif
        </div>

        <div class="overflow-x-auto rounded-xl border border-slate-200">
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
                        <td>{{ $salaryStructure->name ?? '-' }}</td>
                        <td>{{ $salaryStructure->company?->name }}</td>
                        <td>{{ $salaryStructure->base_salary }}</td>
                        <td>{{ $salaryStructure->hra }}</td>
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
@endsection
