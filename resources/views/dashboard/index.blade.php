@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="page-shell">
        <section class="page-hero">
            <div>
                <span class="eyebrow"><span class="material-symbols-rounded">insights</span> Role-aware overview</span>
                <h2>Welcome back, {{ auth()->user()->name }}</h2>
                <p>Your dashboard adapts to your permissions, and the portal highlights the most relevant actions for your current role.</p>
            </div>

            <div class="hero-actions">
                @foreach($visibleModules->take(2) as $module)
                    <a href="{{ $module['route'] }}" class="btn btn-ghost">
                        <span class="material-symbols-rounded">{{ $module['icon'] ?? 'arrow_forward' }}</span>
                        {{ $module['label'] }}
                    </a>
                @endforeach
            </div>
        </section>

        @if($dashboardStats->isNotEmpty())
            <section class="stats-grid">
                @foreach($dashboardStats as $stat)
                    <article class="stat-card {{ $stat['tone'] ?? 'teal' }}">
                        <div class="stat-icon">
                            <span class="material-symbols-rounded">{{ $stat['icon'] ?? 'monitoring' }}</span>
                        </div>
                        <p class="stat-label">{{ $stat['label'] }}</p>
                        <p class="stat-value">{{ $stat['value'] }}</p>
                    </article>
                @endforeach
            </section>
        @endif

        <section class="content-grid">
            <div class="stack">
                @if(auth()->user()->isSuperAdmin())
                    <article class="card chart-shell">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div>
                                <h3 class="panel-title">New users added in the last 3 days</h3>
                                <p class="panel-copy">A quick acquisition pulse for the platform. The line rises with each day’s new account count.</p>
                            </div>
                            <div class="chart-legend">
                                <span><span class="chart-dot"></span>User signups</span>
                            </div>
                        </div>

                        @php($labels = $userGrowth['labels'] ?? [])
                        @php($values = $userGrowth['values'] ?? [])
                        @php($maxValue = max(max($values ?: [0]), 1))
                        @php($chartWidth = 420)
                        @php($chartHeight = 180)
                        @php($leftPad = 24)
                        @php($bottomPad = 28)
                        @php($topPad = 12)
                        @php($usableWidth = $chartWidth - ($leftPad * 2))
                        @php($usableHeight = $chartHeight - $bottomPad - $topPad)
                        @php($step = count($values) > 1 ? $usableWidth / (count($values) - 1) : 0)
                        @php($points = collect($values)->values()->map(function ($value, $index) use ($leftPad, $step, $chartHeight, $bottomPad, $usableHeight, $maxValue) {
                            $x = $leftPad + ($index * $step);
                            $y = ($chartHeight - $bottomPad) - (($value / $maxValue) * $usableHeight);
                            return ['x' => round($x, 2), 'y' => round($y, 2), 'value' => $value];
                        }))
                        @php($linePoints = $points->map(fn ($point) => $point['x'].','.$point['y'])->implode(' '))
                        @php($areaPoints = $linePoints.' '.optional($points->last())['x'].','.($chartHeight - $bottomPad).' '.optional($points->first())['x'].','.($chartHeight - $bottomPad))

                        <svg viewBox="0 0 {{ $chartWidth }} {{ $chartHeight + 36 }}" class="chart-svg" role="img" aria-label="New users added in the last three days">
                            <defs>
                                <linearGradient id="userGrowthFill" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="rgba(20, 184, 166, 0.34)" />
                                    <stop offset="100%" stop-color="rgba(20, 184, 166, 0.02)" />
                                </linearGradient>
                            </defs>

                            @foreach(range(0, 3) as $gridLine)
                                @php($y = $topPad + ($usableHeight / 3 * $gridLine))
                                <line x1="{{ $leftPad }}" y1="{{ $y }}" x2="{{ $chartWidth - $leftPad }}" y2="{{ $y }}" class="chart-grid-line" />
                            @endforeach

                            @if($points->isNotEmpty())
                                <polygon points="{{ $areaPoints }}" class="chart-area"></polygon>
                                <polyline points="{{ $linePoints }}" class="chart-line"></polyline>
                                @foreach($points as $index => $point)
                                    <circle cx="{{ $point['x'] }}" cy="{{ $point['y'] }}" r="5" class="chart-point"></circle>
                                    <text x="{{ $point['x'] }}" y="{{ $point['y'] - 12 }}" text-anchor="middle" class="chart-axis-label">{{ $point['value'] }}</text>
                                    <text x="{{ $point['x'] }}" y="{{ $chartHeight + 6 }}" text-anchor="middle" class="chart-axis-label">{{ $labels[$index] ?? '' }}</text>
                                @endforeach
                            @endif
                        </svg>
                    </article>
                @endif

                <article class="card">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="panel-title">Available modules</h3>
                            <p class="panel-copy">Jump directly into the areas you can manage today.</p>
                        </div>
                    </div>

                    <div class="module-grid mt-4">
                        @forelse($visibleModules as $module)
                            <a href="{{ $module['route'] }}" class="module-card">
                                <div class="flex items-start justify-between gap-3">
                                    <span class="stat-icon">
                                        <span class="material-symbols-rounded">{{ $module['icon'] ?? 'arrow_forward' }}</span>
                                    </span>
                                    <span class="material-symbols-rounded text-slate-400">north_east</span>
                                </div>
                                <p class="mt-4 font-medium text-slate-900">{{ $module['label'] }}</p>
                                <p class="mt-2 text-sm leading-6 text-slate-500">{{ $module['description'] ?? 'Open this module.' }}</p>
                            </a>
                        @empty
                            <div class="empty-state">No modules are available for your role yet.</div>
                        @endforelse
                    </div>
                </article>
            </div>

            <aside class="stack">
                <article class="card">
                    <h3 class="panel-title">Quick context</h3>
                    <p class="panel-copy">A short snapshot of what this workspace is optimized for.</p>
                    <div class="metric-list mt-4">
                        <div class="metric-row">
                            <div>
                                <p class="font-medium text-slate-900">Access model</p>
                                <p class="text-sm text-slate-500">Role and permission based</p>
                            </div>
                            <span class="badge badge-success">Active</span>
                        </div>
                        <div class="metric-row">
                            <div>
                                <p class="font-medium text-slate-900">Current user role</p>
                                <p class="text-sm text-slate-500">{{ str(auth()->user()->roles()->pluck('name')->implode(', '))->replace('_', ' ') }}</p>
                            </div>
                            <span class="badge badge-muted">Scoped</span>
                        </div>
                        <div class="metric-row">
                            <div>
                                <p class="font-medium text-slate-900">Experience mode</p>
                                <p class="text-sm text-slate-500">Operational dashboard</p>
                            </div>
                            <span class="badge badge-warning">Live</span>
                        </div>
                    </div>
                </article>
            </aside>
        </section>
    </div>
@endsection
