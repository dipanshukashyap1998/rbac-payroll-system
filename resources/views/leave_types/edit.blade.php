@extends('layouts.app', ['title' => 'Edit Leave Type'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">event_available</span> Leave policy</span>
                <h2>Edit leave type</h2>
                <p>Update the company leave rules when policy changes.</p>
            </div>
        </section>

        <div class="card form-card">
            <form method="POST" action="{{ route('leave-types.update', $leaveType) }}" class="form-grid">
                @csrf
                @method('PUT')
                <div class="split-grid">
                    <div class="field"><label>Name</label><input name="name" value="{{ old('name', $leaveType->name) }}"></div>
                    <div class="field"><label>Code</label><input name="code" value="{{ old('code', $leaveType->code) }}"></div>
                </div>
                <div class="split-grid">
                    <div class="field"><label>Annual allotment (days)</label><input type="number" step="0.01" name="default_days" value="{{ old('default_days', $leaveType->default_days) }}"></div>
                    <div class="field"><label>Max carry forward days</label><input type="number" step="0.01" name="max_carry_forward_days" value="{{ old('max_carry_forward_days', $leaveType->max_carry_forward_days) }}"></div>
                </div>
                <div class="split-grid">
                    <div class="field"><label>Is paid?</label><select name="is_paid"><option value="1" @selected(old('is_paid', $leaveType->is_paid))>Yes</option><option value="0" @selected(old('is_paid', $leaveType->is_paid) === false || old('is_paid', $leaveType->is_paid) === '0')>No</option></select></div>
                    <div class="field"><label>Approval required?</label><select name="requires_approval"><option value="1" @selected(old('requires_approval', $leaveType->requires_approval))>Yes</option><option value="0" @selected(old('requires_approval', $leaveType->requires_approval) === false || old('requires_approval', $leaveType->requires_approval) === '0')>No</option></select></div>
                </div>
                <div class="split-grid">
                    <div class="field"><label>Carry forward?</label><select name="carry_forward"><option value="1" @selected(old('carry_forward', $leaveType->carry_forward))>Yes</option><option value="0" @selected(old('carry_forward', $leaveType->carry_forward) === false || old('carry_forward', $leaveType->carry_forward) === '0')>No</option></select></div>
                    <div class="field"><label>Active?</label><select name="is_active"><option value="1" @selected(old('is_active', $leaveType->is_active))>Yes</option><option value="0" @selected(old('is_active', $leaveType->is_active) === false || old('is_active', $leaveType->is_active) === '0')>No</option></select></div>
                </div>
                <div class="field"><label>Description</label><textarea name="description" rows="4">{{ old('description', $leaveType->description) }}</textarea></div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Update leave type</button>
                    <a class="btn btn-secondary" href="{{ route('leave-types.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
