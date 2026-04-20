@extends('layouts.app', ['title' => 'Edit Permission'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">vpn_key</span> Access maintenance</span>
                <h2>Edit permission</h2>
                <p>Keep permission names clear and stable so role policies remain predictable.</p>
            </div>
        </section>

        <div class="card form-card" style="max-width:640px;">
            <form method="POST" action="{{ route('permissions.update', $permission) }}">
                @csrf
                @method('PUT')
                <div class="field">
                    <label>Permission Name</label>
                    <input name="name" value="{{ old('name', $permission->name) }}" required>
                </div>
                <div class="actions">
                    <button class="btn btn-primary" type="submit">Update Permission</button>
                    <a class="btn btn-secondary" href="{{ route('permissions.index') }}">Cancel</a>
                </div>
            </form>
        </div>
    </div>
@endsection
