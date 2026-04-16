@extends('layouts.app', ['title' => 'Create Company'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('companies.store') }}">
            @csrf

            <div class="field">
                <label>Name</label>
                <input name="name" value="{{ old('name') }}" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email') }}">
            </div>

            <div class="field">
                <label>Phone</label>
                <input name="phone" value="{{ old('phone') }}">
            </div>

            <div class="field">
                <label>City</label>
                <input name="city" value="{{ old('city') }}">
            </div>

            <div class="field">
                <label>State</label>
                <input name="state" value="{{ old('state') }}">
            </div>

            <div class="field">
                <label>Country</label>
                <input name="country" value="{{ old('country') }}">
            </div>

            <div class="field">
                <label>Status</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Save Company</button>
                <a class="btn btn-secondary" href="{{ route('companies.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
