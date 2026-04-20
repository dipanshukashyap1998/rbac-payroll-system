@extends('layouts.app', ['title' => 'My Salary Structure'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">wallet</span> Personal compensation</span>
                <h2>My salary structure</h2>
                <p>Review the salary structure currently assigned to your employee profile.</p>
            </div>
        </section>

        <div class="card" style="max-width:720px;">
            @if($salaryAssignment)
                <h3 class="panel-title">{{ $salaryAssignment->salaryStructure?->name ?? 'Assigned Structure' }}</h3>
                <p class="panel-copy">Effective from: {{ optional($salaryAssignment->effective_from)->format('d M Y') }}</p>

                <div class="table-wrap mt-4">
                    <table class="table">
                        <tr><th>Base Salary</th><td>{{ number_format($salaryAssignment->salaryStructure?->base_salary ?? 0, 2) }}</td></tr>
                        <tr><th>HRA</th><td>{{ number_format($salaryAssignment->salaryStructure?->hra ?? 0, 2) }}</td></tr>
                        <tr><th>Allowances</th><td>{{ number_format($salaryAssignment->salaryStructure?->allowances ?? 0, 2) }}</td></tr>
                        <tr><th>Deductions</th><td>{{ number_format($salaryAssignment->salaryStructure?->deductions ?? 0, 2) }}</td></tr>
                    </table>
                </div>
            @else
                <div class="empty-state">No salary structure has been assigned yet.</div>
            @endif
        </div>
    </div>
@endsection
