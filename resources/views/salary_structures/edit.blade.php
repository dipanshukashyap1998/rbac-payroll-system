@extends('layouts.app', ['title' => 'Edit Salary Structure'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">tune</span> Compensation maintenance</span>
                <h2>Edit salary structure</h2>
                <p>Keep compensation components current while preserving a clean admin workflow.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('salary-structures.update', $salaryStructure) }}">
                @csrf
                @method('PUT')

                <div class="form-grid split-grid">
                    <div class="field">
                        <label>Structure Name</label>
                        <input name="name" value="{{ old('name', $salaryStructure->name) }}">
                    </div>

                    <div class="field">
                        <label>Base Salary</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary', $salaryStructure->base_salary) }}" required>
                    </div>

                    <div class="field">
                        <label>HRA</label>
                        <input type="number" step="0.01" name="hra" value="{{ old('hra', $salaryStructure->hra) }}">
                    </div>

                    <div class="field">
                        <label>Allowances</label>
                        <input type="number" step="0.01" name="allowances" value="{{ old('allowances', $salaryStructure->allowances) }}">
                    </div>

                    <div class="field">
                        <label>Deductions</label>
                        <input type="number" step="0.01" name="deductions" value="{{ old('deductions', $salaryStructure->deductions) }}">
                    </div>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Update</button>
                    <a class="btn btn-secondary" href="{{ route('salary-structures.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
