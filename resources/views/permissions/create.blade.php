@extends('layouts.app', ['title' => 'Create Permission'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">key</span> Access definition</span>
                <h2>Create permission</h2>
                <p>Name permissions clearly so the access matrix stays easy to reason about at scale.</p>
            </div>
        </section>

        <div class="card form-card" style="max-width:640px;">
            <form method="POST" action="{{ route('permissions.store') }}">
                @csrf
                <div class="field">
                    <label>Permission Name</label>
                    <input name="name" value="{{ old('name') }}" placeholder="example: payroll.approve" required>
                </div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Save Permission</button>
                    <a class="btn btn-secondary" href="{{ route('permissions.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
