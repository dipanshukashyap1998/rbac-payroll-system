@extends('layouts.app', ['title' => 'Create Salary Structure'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">payments</span> Compensation template</span>
                <h2>Create salary structure</h2>
                <p>Capture the main salary components in a form that’s easier to scan and maintain.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('salary-structures.store') }}">
                @csrf

                <div class="form-grid split-grid">
                    <div class="field">
                        <label>Structure Name</label>
                        <input name="name" value="{{ old('name') }}">
                    </div>

                    <div class="field">
                        <label>Base Salary</label>
                        <input type="number" step="0.01" name="base_salary" value="{{ old('base_salary') }}" required>
                    </div>

                    <div class="field">
                        <label>HRA</label>
                        <input type="number" step="0.01" name="hra" value="{{ old('hra', 0) }}">
                    </div>

                    <div class="field">
                        <label>Allowances</label>
                        <input type="number" step="0.01" name="allowances" value="{{ old('allowances', 0) }}">
                    </div>

                    <div class="field">
                        <label>Deductions</label>
                        <input type="number" step="0.01" name="deductions" value="{{ old('deductions', 0) }}">
                    </div>
                </div>

                <div class="actions">
                    <button class="btn btn-primary" type="submit">Save</button>
                    <a class="btn btn-secondary" href="{{ route('salary-structures.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
