@extends('layouts.app', ['title' => 'Create Salary Structure'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('salary-structures.store') }}">
            @csrf

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

            <div class="actions">
                <button class="btn btn-primary" type="submit">Save</button>
                <a class="btn btn-secondary" href="{{ route('salary-structures.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
