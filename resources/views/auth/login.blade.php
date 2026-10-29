<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff login – {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-600 antialiased">
<div class="grid min-h-screen lg:grid-cols-2">

    {{-- Brand panel --}}
    <div class="relative hidden overflow-hidden bg-linear-to-br from-brand-600 to-brand-900 p-12 text-white lg:flex lg:flex-col lg:justify-between">
        <div class="pointer-events-none absolute -top-20 -right-20 size-96 rounded-full bg-white/10 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-16 size-80 rounded-full bg-brand-300/20 blur-3xl"></div>

        <a href="{{ route('home') }}" class="relative flex items-center gap-2.5">
            <span class="flex size-10 items-center justify-center rounded-xl bg-white/20">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5"><path d="M12 5v14M5 12h14" /></svg>
            </span>
            <span class="text-xl font-bold">{{ config('app.name') }}</span>
        </a>

        <div class="relative">
            <h1 class="text-4xl font-extrabold tracking-tight text-balance">Everything your clinic needs, in one place.</h1>
            <p class="mt-4 max-w-md text-lg text-brand-100">Patients, appointments and records, organized and secure for your whole team.</p>
        </div>

        <p class="relative text-sm text-brand-200">&copy; {{ date('Y') }} {{ config('app.name') }}</p>
    </div>

    {{-- Form --}}
    <div class="flex items-center justify-center bg-slate-50 px-4 py-12 sm:px-8">
        <div class="w-full max-w-md">
            <a href="{{ route('home') }}" class="mb-8 flex items-center gap-2.5 lg:hidden">
                <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5"><path d="M12 5v14M5 12h14" /></svg>
                </span>
                <span class="text-lg font-bold text-slate-900">{{ config('app.name') }}</span>
            </a>

            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Welcome back</h2>
            <p class="mt-2 text-slate-600">Sign in to access the clinic dashboard.</p>

            <form method="POST" action="{{ url('/login') }}" class="mt-8 space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="mt-1.5 w-full rounded-xl border px-4 py-3 text-sm outline-hidden focus:ring-2 {{ $errors->has('email') ? 'border-red-400 focus:ring-red-100' : 'border-slate-300 focus:border-brand-500 focus:ring-brand-200' }}">
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" class="size-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    Remember me
                </label>

                <button type="submit"
                        class="w-full rounded-xl bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                    Sign in
                </button>
            </form>

            <p class="mt-6 text-center text-sm">
                <a href="{{ route('home') }}" class="font-medium text-brand-700 hover:text-brand-600">← Back to website</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>