@extends('layouts.app', ['title' => 'Create Permission'])

@section('content')
    <div class="card" style="max-width:640px;">
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
@endsection
