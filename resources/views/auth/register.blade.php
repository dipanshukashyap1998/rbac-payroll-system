<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'ui-sans-serif', 'system-ui']
                    },
                    boxShadow: {
                        glow: '0 30px 80px rgba(15, 23, 42, 0.18)'
                    }
                }
            }
        }
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(34,197,94,0.18),_transparent_28%),radial-gradient(circle_at_bottom_right,_rgba(14,165,233,0.18),_transparent_30%),linear-gradient(135deg,_#020617_0%,_#0f172a_45%,_#111827_100%)]"></div>
        <div class="absolute left-[-6rem] top-16 h-72 w-72 rounded-full bg-emerald-400/20 blur-3xl"></div>
        <div class="absolute bottom-[-5rem] right-[-4rem] h-80 w-80 rounded-full bg-sky-500/20 blur-3xl"></div>

        <div class="relative mx-auto flex min-h-screen max-w-7xl items-center px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid w-full gap-8 lg:grid-cols-[1.1fr_.9fr]">
                <section class="hidden rounded-[2rem] border border-white/10 bg-white/5 p-10 shadow-glow backdrop-blur lg:flex lg:flex-col lg:justify-between">
                    <div>
                        <span class="inline-flex items-center gap-2 rounded-full border border-emerald-300/25 bg-emerald-400/10 px-4 py-2 text-sm text-emerald-100">
                            <span class="material-symbols-rounded">verified_user</span>
                            Admin onboarding
                        </span>
                        <h1 class="mt-6 max-w-xl text-5xl font-semibold leading-tight text-white">Launch your payroll workspace with a clean first-time setup.</h1>
                        <p class="mt-5 max-w-lg text-base leading-7 text-slate-300">
                            Create your account, sign back in, and finish by creating your company profile. Until your company exists, the portal stays safely locked to onboarding only.
                        </p>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-3">
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                            <p class="text-sm font-medium text-white">Step 1</p>
                            <p class="mt-2 text-sm text-slate-300">Register as a new admin account.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                            <p class="text-sm font-medium text-white">Step 2</p>
                            <p class="mt-2 text-sm text-slate-300">Log in again with your new credentials.</p>
                        </div>
                        <div class="rounded-2xl border border-white/10 bg-white/10 p-4">
                            <p class="text-sm font-medium text-white">Step 3</p>
                            <p class="mt-2 text-sm text-slate-300">Create your company to unlock the portal.</p>
                        </div>
                    </div>
                </section>

                <section class="rounded-[2rem] border border-white/10 bg-white/95 p-6 text-slate-800 shadow-glow backdrop-blur sm:p-8 lg:p-10">
                    <div class="mb-8">
                        <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50 px-3 py-1 text-sm font-medium text-emerald-700">
                            <span class="material-symbols-rounded">person_add</span>
                            Create account
                        </span>
                        <h2 class="mt-4 text-3xl font-semibold tracking-tight text-slate-900">Build your admin access</h2>
                        <p class="mt-2 text-sm leading-6 text-slate-500">Use your work details here. We’ll create an admin account and take you back to login once registration is complete.</p>
                    </div>

                    @if($errors->any())
                        <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ $errors->first() }}</div>
                    @endif

                    <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
                        @csrf

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Full name</span>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                        </label>

                        <label class="block">
                            <span class="mb-2 block text-sm font-medium text-slate-700">Email address</span>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                        </label>

                        <div class="grid gap-5 sm:grid-cols-2">
                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Password</span>
                                <input id="password" type="password" name="password" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                            </label>

                            <label class="block">
                                <span class="mb-2 block text-sm font-medium text-slate-700">Confirm password</span>
                                <input id="password_confirmation" type="password" name="password_confirmation" required class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-emerald-400 focus:ring-4 focus:ring-emerald-100">
                            </label>
                        </div>

                        <button class="inline-flex w-full items-center justify-center gap-2 rounded-2xl bg-emerald-600 px-4 py-3 text-sm font-medium text-white transition hover:bg-emerald-700" type="submit">
                            <span class="material-symbols-rounded">app_registration</span>
                            Create my account
                        </button>
                    </form>

                    <div class="mt-6 rounded-2xl border border-slate-200 bg-slate-50 px-4 py-4 text-sm text-slate-600">
                        Already registered?
                        <a class="font-semibold text-emerald-700 transition hover:text-emerald-800" href="{{ route('login') }}">Return to login</a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</body>
</html>
