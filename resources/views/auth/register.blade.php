<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create account – {{ config('app.name') }}</title>

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
            <h1 class="text-4xl font-extrabold tracking-tight text-balance">Your health, scheduled in a few clicks.</h1>
            <p class="mt-4 max-w-md text-lg text-brand-100">Create an account, pick a doctor and choose a time that suits you.</p>
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

            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Create your account</h2>
            <p class="mt-2 text-slate-600">Register once to book and manage your appointments online.</p>

            <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
                @csrf
                <div class="hidden" aria-hidden="true"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>

                @foreach ([['name', 'Full name', 'text', 'name'], ['email', 'Email', 'email', 'email'], ['phone', 'Phone (optional)', 'tel', 'tel']] as [$field, $lbl, $type, $ac])
                    <div>
                        <label for="{{ $field }}" class="block text-sm font-medium text-slate-700">{{ $lbl }}</label>
                        <input id="{{ $field }}" name="{{ $field }}" type="{{ $type }}" value="{{ old($field) }}" autocomplete="{{ $ac }}" @if ($field !== 'phone') required @endif
                               class="mt-1.5 w-full rounded-xl border px-4 py-3 text-sm outline-hidden focus:ring-2 {{ $errors->has($field) ? 'border-red-400 focus:ring-red-100' : 'border-slate-300 focus:border-brand-500 focus:ring-brand-200' }}">
                        @error($field)<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                @endforeach

                <div>
                    <label for="password" class="block text-sm font-medium text-slate-700">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" minlength="8"
                           class="mt-1.5 w-full rounded-xl border px-4 py-3 text-sm outline-hidden focus:ring-2 {{ $errors->has('password') ? 'border-red-400 focus:ring-red-100' : 'border-slate-300 focus:border-brand-500 focus:ring-brand-200' }}">
                    <p class="mt-1.5 text-xs text-slate-500">At least 8 characters, with letters and numbers.</p>
                    @error('password')<p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>

                <button type="submit" class="w-full rounded-xl bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">Create account</button>
            </form>

            <p class="mt-6 text-center text-sm text-slate-600">Already have an account? <a href="{{ route('login') }}" class="font-semibold text-brand-700 hover:text-brand-600">Sign in</a></p>

            <p class="mt-6 text-center text-sm">
                <a href="{{ route('home') }}" class="font-medium text-brand-700 hover:text-brand-600">← Back to website</a>
            </p>
        </div>
    </div>
</div>
</body>
</html>