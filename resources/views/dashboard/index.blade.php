@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="card">
        <h3 class="text-xl font-semibold text-slate-900">Welcome back, {{ auth()->user()->name }}</h3>
        <p class="mt-1 text-sm text-slate-500">Your modules are filtered by role scope and permissions.</p>

        <div class="mt-5 grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
            @forelse($visibleModules as $module)
                <a href="{{ $module['route'] }}" class="group rounded-2xl border border-slate-200 bg-white p-4 transition hover:-translate-y-0.5 hover:border-blue-200 hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-slate-800">{{ $module['label'] }}</p>
                        <span class="material-symbols-rounded text-slate-400 group-hover:text-blue-600">arrow_forward</span>
                    </div>
                </a>
            @empty
                <div class="rounded-xl border border-dashed border-slate-300 p-6 text-sm text-slate-500">No modules available for your role yet.</div>
            @endforelse
        </div>
    </div>
@endsection
