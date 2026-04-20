<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
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
    </style>
</head>
<body class="min-h-full bg-slate-950 text-slate-100">
    <div class="relative min-h-screen overflow-hidden">
        <div class="absolute -left-24 top-[-5rem] h-72 w-72 rounded-full bg-cyan-400/30 blur-3xl"></div>
        <div class="absolute bottom-[-4rem] right-[-2rem] h-72 w-72 rounded-full bg-indigo-500/25 blur-3xl"></div>

        <div class="relative mx-auto flex min-h-screen max-w-6xl items-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="grid w-full gap-8 lg:grid-cols-2">
                <div class="hidden lg:flex lg:flex-col lg:justify-center">
                    <span class="mb-4 inline-flex w-fit items-center gap-2 rounded-full border border-white/20 bg-white/10 px-4 py-2 text-sm">
                        <span class="material-symbols-rounded">shield_lock</span>
                        Secure RBAC Platform
                    </span>
                    <h1 class="text-4xl font-semibold leading-tight text-white">Sign in to manage payroll, roles and permissions.</h1>
                    <p class="mt-4 max-w-md text-slate-300">Modern access control for superadmin, admins, and employees in a single dashboard.</p>
                </div>

                <div class="rounded-3xl border border-white/10 bg-white/95 p-6 text-slate-800 shadow-2xl backdrop-blur sm:p-8">
                    <div class="mb-6">
                        <h2 class="text-2xl font-semibold">Welcome back</h2>
                        <p class="mt-1 text-sm text-slate-500">Enter your credentials to continue</p>
                    </div>

                    @if(session('status'))
                        <div class="mb-4 rounded-xl border border-sky-200 bg-sky-50 px-4 py-3 text-sm text-sky-700">{{ session('status') }}</div>
                    @endif

                    @if($errors->any())
                        <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
                        @csrf

                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-slate-600">Email</span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="block">
                            <span class="mb-1.5 block text-sm font-medium text-slate-600">Password</span>
                            <input id="password" type="password" name="password" required class="w-full rounded-xl border border-slate-300 px-4 py-2.5 text-sm outline-none transition focus:border-blue-400 focus:ring-4 focus:ring-blue-100">
                        </label>

                        <label class="inline-flex items-center gap-2 text-sm text-slate-600">
                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                            Remember me
                        </label>

                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-blue-700" type="submit">
                            <span class="material-symbols-rounded">login</span>
                            Login
                        </button>
                    </form>

                    <div class="mt-4 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                        New here?
                        <a class="font-semibold text-blue-700 transition hover:text-blue-800" href="{{ route('register') }}">Create an admin account</a>
                    </div>

                    <div class="my-6 flex items-center gap-3 text-xs uppercase tracking-wider text-slate-400">
                        <span class="h-px flex-1 bg-slate-200"></span>
                        Continue with
                        <span class="h-px flex-1 bg-slate-200"></span>
                    </div>

                    <div class="grid grid-cols-1 gap-2">
                        <a class="inline-flex items-center justify-center gap-1 rounded-xl border border-slate-300 px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50" href="{{ route('social.login', 'google') }}">Google</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
