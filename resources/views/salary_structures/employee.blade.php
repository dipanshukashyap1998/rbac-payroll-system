@extends('layouts.app', ['title' => 'My Salary Structure'])

@section('content')
    <div class="card" style="max-width:720px;">
        @if($salaryAssignment)
            <h3 style="margin-top:0;">{{ $salaryAssignment->salaryStructure?->name ?? 'Assigned Structure' }}</h3>
            <p style="color:#6b7280;">Effective from: {{ optional($salaryAssignment->effective_from)->format('d M Y') }}</p>

            <table class="table">
                <tr><th>Base Salary</th><td>{{ $salaryAssignment->salaryStructure?->base_salary }}</td></tr>
                <tr><th>HRA</th><td>{{ $salaryAssignment->salaryStructure?->hra }}</td></tr>
                <tr><th>Allowances</th><td>{{ $salaryAssignment->salaryStructure?->allowances }}</td></tr>
                <tr><th>Deductions</th><td>{{ $salaryAssignment->salaryStructure?->deductions }}</td></tr>
            </table>
        @else
            <p style="margin:0; color:#6b7280;">No salary structure has been assigned yet.</p>
        @endif
    </div>
@endsection
