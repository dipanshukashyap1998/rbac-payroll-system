<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'RBAC System' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#eff6ff',
                            100: '#dbeafe',
                            500: '#2563eb',
                            600: '#1d4ed8',
                            700: '#1e40af'
                        }
                    },
                    boxShadow: {
                        soft: '0 18px 60px rgba(15, 23, 42, 0.10)',
                        float: '0 26px 80px rgba(15, 23, 42, 0.14)'
                    },
                    fontFamily: {
                        sans: ['Outfit', 'ui-sans-serif', 'system-ui']
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" rel="stylesheet" />

    <style>
        :root {
            --app-bg: #eef4f8;
            --panel-bg: rgba(255, 255, 255, 0.82);
            --panel-border: rgba(148, 163, 184, 0.2);
            --text-main: #0f172a;
            --text-muted: #64748b;
            --gradient-accent: linear-gradient(135deg, rgba(13, 148, 136, 0.95), rgba(14, 165, 233, 0.95));
        }

        .material-symbols-rounded {
            font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            font-size: 18px;
            line-height: 1;
        }

        .shell-bg {
            background:
                radial-gradient(circle at top left, rgba(20, 184, 166, 0.16), transparent 26%),
                radial-gradient(circle at bottom right, rgba(14, 165, 233, 0.16), transparent 28%),
                linear-gradient(180deg, #f7fbfd 0%, #eef4f8 100%);
        }

        .card {
            border-radius: 1.5rem;
            border: 1px solid var(--panel-border);
            background: var(--panel-bg);
            padding: 1.4rem;
            box-shadow: 0 16px 50px rgba(15, 23, 42, 0.06);
            backdrop-filter: blur(18px);
        }

        .page-shell {
            display: grid;
            gap: 1.35rem;
        }

        .page-hero {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
            border-radius: 1.75rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.96), rgba(15, 118, 110, 0.92));
            padding: 1.5rem;
            color: white;
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
        }

        .page-hero h2 {
            margin: 0;
            font-size: 1.55rem;
            font-weight: 600;
            letter-spacing: -.02em;
        }

        .page-hero p {
            margin: .55rem 0 0;
            max-width: 44rem;
            color: rgba(226, 232, 240, 0.86);
            font-size: .95rem;
            line-height: 1.6;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border-radius: 999px;
            border: 1px solid rgba(255,255,255,.14);
            background: rgba(255,255,255,.08);
            padding: .4rem .8rem;
            font-size: .73rem;
            font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hero-actions,
        .page-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .65rem;
        }

        .stats-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(190px, 1fr));
        }

        .stat-card {
            position: relative;
            overflow: hidden;
            border-radius: 1.35rem;
            border: 1px solid rgba(148, 163, 184, 0.18);
            background: rgba(255,255,255,.76);
            padding: 1.15rem;
        }

        .stat-card::after {
            content: '';
            position: absolute;
            inset: auto -20px -36px auto;
            width: 86px;
            height: 86px;
            border-radius: 999px;
            opacity: .12;
        }

        .stat-card.teal::after { background: #14b8a6; }
        .stat-card.amber::after { background: #f59e0b; }
        .stat-card.rose::after { background: #f43f5e; }
        .stat-card.slate::after { background: #475569; }

        .stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2.6rem;
            height: 2.6rem;
            border-radius: 1rem;
            background: rgba(15, 23, 42, .06);
            color: #0f172a;
        }

        .stat-label {
            margin-top: .9rem;
            color: var(--text-muted);
            font-size: .8rem;
            text-transform: uppercase;
            letter-spacing: .07em;
        }

        .stat-value {
            margin-top: .45rem;
            color: var(--text-main);
            font-size: 1.55rem;
            font-weight: 600;
            letter-spacing: -.03em;
            line-height: 1.2;
        }

        .module-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }

        .module-card {
            display: block;
            border-radius: 1.45rem;
            border: 1px solid rgba(148, 163, 184, .18);
            background: rgba(255,255,255,.75);
            padding: 1.2rem;
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .module-card:hover {
            transform: translateY(-3px);
            border-color: rgba(45, 212, 191, .45);
            box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        }

        .module-card p {
            margin: 0;
        }

        .panel-title {
            margin: 0;
            font-size: 1.05rem;
            font-weight: 600;
            color: var(--text-main);
        }

        .panel-copy {
            margin-top: .45rem;
            color: var(--text-muted);
            font-size: .92rem;
            line-height: 1.55;
        }

        .stack {
            display: grid;
            gap: 1rem;
        }

        .split-grid {
            display: grid;
            gap: 1rem;
        }

        .content-grid {
            display: grid;
            gap: 1rem;
            grid-template-columns: minmax(0, 1fr);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            justify-content: center;
            border-radius: .95rem;
            font-size: .875rem;
            font-weight: 500;
            padding: .7rem 1rem;
            border: 1px solid transparent;
            transition: .2s ease;
        }

        .btn-primary { background: var(--gradient-accent); color: #fff; box-shadow: 0 12px 28px rgba(20, 184, 166, .22); }
        .btn-primary:hover { transform: translateY(-1px); }
        .btn-secondary { background: rgba(255,255,255,.72); color: #334155; border-color: rgba(148, 163, 184, .26); }
        .btn-secondary:hover { background: #fff; border-color: rgba(45, 212, 191, .32); }
        .btn-danger { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
        .btn-danger:hover { background: #ffe4e6; }
        .btn-ghost { background: rgba(255,255,255,.08); color: white; border-color: rgba(255,255,255,.16); }
        .btn-ghost:hover { background: rgba(255,255,255,.14); }

        .form-card {
            max-width: 900px;
        }

        .form-grid {
            display: grid;
            gap: 1rem;
        }

        .field {
            margin-bottom: 1rem;
        }

        .field label {
            display: block;
            margin-bottom: .45rem;
            color: #475569;
            font-size: .85rem;
            font-weight: 600;
            letter-spacing: .01em;
        }

        .field .help {
            margin-top: .45rem;
            color: #64748b;
            font-size: .78rem;
            line-height: 1.5;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid rgba(148, 163, 184, .32);
            border-radius: 1rem;
            padding: .8rem .95rem;
            font-size: .9rem;
            background: rgba(255,255,255,.82);
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: #2dd4bf;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(45, 212, 191, 0.14);
        }

        .table-wrap {
            overflow-x: auto;
            border-radius: 1.25rem;
            border: 1px solid rgba(148, 163, 184, .18);
            background: rgba(255,255,255,.78);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            font-size: .92rem;
        }

        .table th,
        .table td {
            border-bottom: 1px solid rgba(226, 232, 240, .9);
            padding: .9rem .85rem;
            text-align: left;
            vertical-align: top;
        }

        .table th {
            color: #475569;
            font-weight: 600;
            background: rgba(248, 250, 252, .96);
        }

        .table tbody tr:hover {
            background: rgba(248, 250, 252, .75);
        }

        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }

        .status {
            margin-bottom: 1rem;
            border-radius: 1rem;
            border: 1px solid #86efac;
            background: #f0fdf4;
            color: #166534;
            padding: .9rem 1rem;
            font-size: .9rem;
        }

        .error {
            margin-bottom: 1rem;
            border-radius: 1rem;
            border: 1px solid #fda4af;
            background: #fff1f2;
            color: #be123c;
            padding: .9rem 1rem;
            font-size: .9rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            border-radius: 999px;
            padding: .35rem .7rem;
            font-size: .73rem;
            font-weight: 600;
        }

        .badge-success { background: #ecfdf5; color: #047857; }
        .badge-muted { background: #f1f5f9; color: #475569; }
        .badge-warning { background: #fff7ed; color: #c2410c; }
        .metric-list {
            display: grid;
            gap: .85rem;
        }

        .metric-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: .75rem;
            padding-bottom: .85rem;
            border-bottom: 1px dashed rgba(148, 163, 184, .28);
        }

        .metric-row:last-child {
            padding-bottom: 0;
            border-bottom: none;
        }

        .chart-shell {
            display: grid;
            gap: 1rem;
        }

        .chart-legend {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            color: #64748b;
            font-size: .82rem;
        }

        .chart-dot {
            display: inline-flex;
            width: .7rem;
            height: .7rem;
            border-radius: 999px;
            background: #14b8a6;
            margin-right: .45rem;
        }

        .chart-svg {
            width: 100%;
            height: 240px;
            overflow: visible;
        }

        .chart-grid-line {
            stroke: rgba(148, 163, 184, .20);
            stroke-width: 1;
        }

        .chart-axis-label {
            fill: #64748b;
            font-size: 12px;
        }

        .chart-line {
            fill: none;
            stroke: #14b8a6;
            stroke-width: 4;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .chart-area {
            fill: url(#userGrowthFill);
        }

        .chart-point {
            fill: #0f172a;
            stroke: #ecfeff;
            stroke-width: 4;
        }

        .toolbar-card {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
        }

        .empty-state {
            border-radius: 1.4rem;
            border: 1px dashed rgba(148, 163, 184, .35);
            background: rgba(248, 250, 252, .7);
            padding: 1.4rem;
            color: #64748b;
        }

        @media (min-width: 1024px) {
            .content-grid {
                grid-template-columns: minmax(0, 1.35fr) minmax(280px, .85fr);
                align-items: start;
            }

            .split-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }
    </style>
</head>
<body class="h-full shell-bg text-slate-800">
@php($currentUser = auth()->user())
<div class="min-h-screen lg:grid lg:grid-cols-[18rem_1fr]">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-white/40 bg-slate-950/92 px-4 py-6 text-slate-100 shadow-float backdrop-blur transition-transform lg:static lg:translate-x-0 lg:shadow-none">
        <div class="mb-6 flex items-center justify-between lg:justify-start">
            <div>
                <p class="text-xs uppercase tracking-[0.22em] text-teal-200/80">RBAC Payroll</p>
                <p class="text-lg font-semibold text-white">Control Center</p>
            </div>
            <button type="button" class="rounded-lg p-2 text-slate-300 hover:bg-white/10 lg:hidden" onclick="toggleSidebar()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        <div class="mb-5 rounded-2xl border border-white/10 bg-white/5 p-4">
            <p class="text-sm font-medium text-white">{{ $currentUser?->name }}</p>
            <p class="mt-1 text-xs uppercase tracking-[0.18em] text-slate-400">{{ str($currentUser?->roles()->pluck('name')->implode(', '))->replace('_', ' ') }}</p>
        </div>

        <nav class="space-y-1.5 text-sm">
            @if($currentUser?->hasPermission('dashboard.view'))
                <a class="{{ request()->routeIs('dashboard') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('dashboard') }}">
                    <span class="material-symbols-rounded">dashboard</span> Dashboard
                </a>
            @endif

            @if($currentUser?->hasPermission('company.view'))
                <a class="{{ request()->routeIs('companies.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('companies.index') }}">
                    <span class="material-symbols-rounded">apartment</span> Companies
                </a>
            @endif

            @if($currentUser?->isAdmin() && $currentUser?->hasPermission('employee.view'))
                <a class="{{ request()->routeIs('employees.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('employees.index') }}">
                    <span class="material-symbols-rounded">groups</span> Employees
                </a>
            @endif

            @if($currentUser?->hasPermission('salary_structure.view'))
                <a class="{{ request()->routeIs('salary-structures.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('salary-structures.index') }}">
                    <span class="material-symbols-rounded">account_balance_wallet</span> Salary Structure
                </a>
            @endif

            @if($currentUser?->hasPermission('payroll.view'))
                <a class="{{ request()->routeIs('payrolls.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('payrolls.index') }}">
                    <span class="material-symbols-rounded">receipt_long</span> Payrolls
                </a>
            @endif

            @if($currentUser?->hasPermission('payslip.view'))
                <a class="{{ request()->routeIs('payslips.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('payslips.index') }}">
                    <span class="material-symbols-rounded">description</span> Payslips
                </a>
            @endif

            @if($currentUser?->hasPermission('audit_log.view'))
                <a class="{{ request()->routeIs('audit-logs.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('audit-logs.index') }}">
                    <span class="material-symbols-rounded">history</span> Audit Logs
                </a>
            @endif

            @if($currentUser?->hasPermission('role.view'))
                <a class="{{ request()->routeIs('roles.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('roles.index') }}">
                    <span class="material-symbols-rounded">security</span> Roles
                </a>
            @endif

            @if($currentUser?->hasPermission('permission.view'))
                <a class="{{ request()->routeIs('permissions.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('permissions.index') }}">
                    <span class="material-symbols-rounded">key</span> Permissions
                </a>
            @endif

            @if($currentUser?->hasPermission('user.view'))
                <a class="{{ request()->routeIs('users.*') ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-300 hover:bg-white/10 hover:text-white' }} flex items-center gap-2 rounded-2xl px-3 py-2.5" href="{{ route('users.index') }}">
                    <span class="material-symbols-rounded">person</span> Users
                </a>
            @endif
        </nav>
    </aside>

    <main class="min-w-0 p-4 sm:p-6">
        <div class="mb-6 flex items-center justify-between gap-3 rounded-[1.6rem] border border-white/40 bg-white/70 px-4 py-3 shadow-soft backdrop-blur sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" class="rounded-xl border border-slate-200 p-2 text-slate-600 hover:bg-slate-100 lg:hidden" onclick="toggleSidebar()">
                    <span class="material-symbols-rounded">menu</span>
                </button>
                <div>
                    <p class="text-xs uppercase tracking-[0.22em] text-slate-400">Workspace</p>
                    <h1 class="text-lg font-semibold text-slate-900">{{ $title ?? 'Module' }}</h1>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-secondary" type="submit">
                    <span class="material-symbols-rounded">logout</span>
                    Logout
                </button>
            </form>
        </div>

        @if(session('status'))
            <div class="status">{{ session('status') }}</div>
        @endif

        @if($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        @yield('content')
    </main>
</div>

<div id="sidebarBackdrop" class="fixed inset-0 z-30 hidden bg-slate-900/40 lg:hidden" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');

        sidebar.classList.toggle('-translate-x-full');
        backdrop.classList.toggle('hidden');
    }
</script>
</body>
</html>
