@extends('layouts.app', ['title' => 'Edit Company'])

@section('content')
    <div class="card" style="max-width:700px;">
        <form method="POST" action="{{ route('companies.update', $company) }}">
            @csrf
            @method('PUT')

            <div class="field">
                <label>Name</label>
                <input name="name" value="{{ old('name', $company->name) }}" required>
            </div>

            <div class="field">
                <label>Email</label>
                <input type="email" name="email" value="{{ old('email', $company->email) }}">
            </div>

            <div class="field">
                <label>Phone</label>
                <input name="phone" value="{{ old('phone', $company->phone) }}">
            </div>

            <div class="field">
                <label>City</label>
                <input name="city" value="{{ old('city', $company->city) }}">
            </div>

            <div class="field">
                <label>State</label>
                <input name="state" value="{{ old('state', $company->state) }}">
            </div>

            <div class="field">
                <label>Country</label>
                <input name="country" value="{{ old('country', $company->country) }}">
            </div>

            <div class="field">
                <label>Status</label>
                <select name="status">
                    @foreach(['active','inactive','suspended'] as $status)
                        <option value="{{ $status }}" @selected(old('status', $company->status) === $status)>{{ ucfirst($status) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="actions">
                <button class="btn btn-primary" type="submit">Update</button>
                <a class="btn btn-secondary" href="{{ route('companies.index') }}">Cancel</a>
            </div>
        </form>
    </div>
@endsection
