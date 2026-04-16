@extends('layouts.app', ['title' => 'Edit Permission'])

@section('content')
    <div class="card" style="max-width:640px;">
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
@endsection
