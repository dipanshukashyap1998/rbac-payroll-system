@extends('layouts.app', ['title' => 'Create Leave Type'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">event_available</span> Leave policy</span>
                <h2>Create leave type</h2>
                <p>Add a company-specific leave category with its own annual allotment and approval behavior.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('leave-types.store') }}" class="form-grid">
                @csrf
                <div class="split-grid">
                    <div class="field"><label>Name</label><input name="name" value="{{ old('name') }}" placeholder="Earned Leave"></div>
                    <div class="field"><label>Code</label><input name="code" value="{{ old('code') }}" placeholder="EL"></div>
                </div>
                <div class="split-grid">
                    <div class="field"><label>Annual allotment (days)</label><input type="number" step="0.01" name="default_days" value="{{ old('default_days', 0) }}"></div>
                    <div class="field"><label>Max carry forward days</label><input type="number" step="0.01" name="max_carry_forward_days" value="{{ old('max_carry_forward_days', 0) }}"></div>
                </div>
                <div class="split-grid">
                    <div class="field"><label>Is paid?</label><select name="is_paid"><option value="1" @selected(old('is_paid', 1))>Yes</option><option value="0" @selected(old('is_paid') === '0')>No</option></select></div>
                    <div class="field"><label>Approval required?</label><select name="requires_approval"><option value="1" @selected(old('requires_approval', 1))>Yes</option><option value="0" @selected(old('requires_approval') === '0')>No</option></select></div>
                </div>
                <div class="field">
                    <label>Carry forward?</label>
                    <select name="carry_forward"><option value="1" @selected(old('carry_forward'))>Yes</option><option value="0" @selected(old('carry_forward', 0) === '0')>No</option></select>
                </div>
                <div class="field"><label>Description</label><textarea name="description" rows="4">{{ old('description') }}</textarea></div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Create leave type</button>
                    <a class="btn btn-secondary" href="{{ route('leave-types.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
