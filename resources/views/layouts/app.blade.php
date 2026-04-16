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
                        soft: '0 10px 30px rgba(2, 6, 23, 0.08)'
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
        .material-symbols-rounded {
            font-variation-settings: 'FILL' 1, 'wght' 500, 'GRAD' 0, 'opsz' 24;
            font-size: 18px;
            line-height: 1;
        }

        .card {
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            background: white;
            padding: 1.25rem;
            box-shadow: 0 10px 30px rgba(2, 6, 23, 0.05);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            border-radius: .7rem;
            font-size: .875rem;
            font-weight: 500;
            padding: .55rem .85rem;
            border: 1px solid transparent;
            transition: .2s ease;
        }

        .btn-primary { background: #2563eb; color: #fff; }
        .btn-primary:hover { background: #1d4ed8; }
        .btn-secondary { background: #fff; color: #334155; border-color: #cbd5e1; }
        .btn-secondary:hover { background: #f8fafc; }
        .btn-danger { background: #fff1f2; color: #be123c; border-color: #fecdd3; }
        .btn-danger:hover { background: #ffe4e6; }

        .field { margin-bottom: 1rem; }
        .field label {
            display: block;
            margin-bottom: .35rem;
            color: #475569;
            font-size: .85rem;
            font-weight: 500;
        }

        .field input,
        .field select,
        .field textarea {
            width: 100%;
            border: 1px solid #cbd5e1;
            border-radius: .75rem;
            padding: .62rem .75rem;
            font-size: .9rem;
            background: #fff;
            outline: none;
        }

        .field input:focus,
        .field select:focus,
        .field textarea:focus {
            border-color: #60a5fa;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
        }

        .table { width: 100%; border-collapse: collapse; font-size: .9rem; }
        .table th, .table td { border-bottom: 1px solid #e2e8f0; padding: .7rem .6rem; text-align: left; }
        .table th { color: #475569; font-weight: 600; background: #f8fafc; }

        .actions { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem; }

        .status {
            margin-bottom: 1rem;
            border-radius: .75rem;
            border: 1px solid #86efac;
            background: #f0fdf4;
            color: #166534;
            padding: .72rem .85rem;
            font-size: .9rem;
        }

        .error {
            margin-bottom: 1rem;
            border-radius: .75rem;
            border: 1px solid #fda4af;
            background: #fff1f2;
            color: #be123c;
            padding: .72rem .85rem;
            font-size: .9rem;
        }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-800">
@php($currentUser = auth()->user())
<div class="min-h-screen lg:grid lg:grid-cols-[18rem_1fr]">
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full border-r border-slate-200 bg-white/95 px-4 py-6 shadow-soft backdrop-blur transition-transform lg:static lg:translate-x-0 lg:shadow-none">
        <div class="mb-6 flex items-center justify-between lg:justify-start">
            <div>
                <p class="text-xs uppercase tracking-wider text-slate-500">RBAC Payroll</p>
                <p class="text-lg font-semibold text-slate-900">Control Center</p>
            </div>
            <button type="button" class="rounded-lg p-2 text-slate-500 hover:bg-slate-100 lg:hidden" onclick="toggleSidebar()">
                <span class="material-symbols-rounded">close</span>
            </button>
        </div>

        <div class="mb-5 rounded-xl border border-slate-200 bg-slate-50 p-3">
            <p class="text-sm font-medium text-slate-900">{{ $currentUser?->name }}</p>
            <p class="mt-1 text-xs text-slate-500">{{ $currentUser?->roles()->pluck('name')->implode(', ') }}</p>
        </div>

        <nav class="space-y-1.5 text-sm">
            @if($currentUser?->hasPermission('dashboard.view'))
                <a class="{{ request()->routeIs('dashboard') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('dashboard') }}">
                    <span class="material-symbols-rounded">dashboard</span> Dashboard
                </a>
            @endif

            @if($currentUser?->hasPermission('company.view'))
                <a class="{{ request()->routeIs('companies.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('companies.index') }}">
                    <span class="material-symbols-rounded">apartment</span> Companies
                </a>
            @endif

            @if($currentUser?->hasPermission('employee.view'))
                <a class="{{ request()->routeIs('employees.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('employees.index') }}">
                    <span class="material-symbols-rounded">groups</span> Employees
                </a>
            @endif

            @if($currentUser?->hasPermission('salary_structure.view'))
                <a class="{{ request()->routeIs('salary-structures.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('salary-structures.index') }}">
                    <span class="material-symbols-rounded">account_balance_wallet</span> Salary Structure
                </a>
            @endif

            @if($currentUser?->hasPermission('payroll.view'))
                <a class="{{ request()->routeIs('payrolls.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('payrolls.index') }}">
                    <span class="material-symbols-rounded">receipt_long</span> Payrolls
                </a>
            @endif

            @if($currentUser?->hasPermission('payslip.view'))
                <a class="{{ request()->routeIs('payslips.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('payslips.index') }}">
                    <span class="material-symbols-rounded">description</span> Payslips
                </a>
            @endif

            @if($currentUser?->hasPermission('audit_log.view'))
                <a class="{{ request()->routeIs('audit-logs.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('audit-logs.index') }}">
                    <span class="material-symbols-rounded">history</span> Audit Logs
                </a>
            @endif

            @if($currentUser?->hasPermission('role.view'))
                <a class="{{ request()->routeIs('roles.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('roles.index') }}">
                    <span class="material-symbols-rounded">security</span> Roles
                </a>
            @endif

            @if($currentUser?->hasPermission('permission.view'))
                <a class="{{ request()->routeIs('permissions.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('permissions.index') }}">
                    <span class="material-symbols-rounded">key</span> Permissions
                </a>
            @endif

            @if($currentUser?->hasPermission('user.view'))
                <a class="{{ request()->routeIs('users.*') ? 'bg-brand-50 text-brand-700' : 'text-slate-600 hover:bg-slate-100' }} flex items-center gap-2 rounded-xl px-3 py-2" href="{{ route('users.index') }}">
                    <span class="material-symbols-rounded">person</span> Users
                </a>
            @endif
        </nav>
    </aside>

    <main class="min-w-0 p-4 sm:p-6">
        <div class="mb-6 flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 shadow-sm sm:px-6">
            <div class="flex items-center gap-3">
                <button type="button" class="rounded-lg border border-slate-300 p-2 text-slate-600 hover:bg-slate-100 lg:hidden" onclick="toggleSidebar()">
                    <span class="material-symbols-rounded">menu</span>
                </button>
                <h1 class="text-lg font-semibold text-slate-900">{{ $title ?? 'Module' }}</h1>
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
