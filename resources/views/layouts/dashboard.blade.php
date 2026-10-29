<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') – {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans text-slate-600 antialiased">

@php
    $user = auth()->user();
    $all = ['admin', 'doctor', 'receptionist'];

    // [label, url, allowed roles, is the page built yet?]
    $nav = [
        ['Dashboard', route('dashboard'), $all, true],
        ['Patients', '#', $all, false],
        ['Appointments', '#', $all, false],
        ['Doctors & staff', '#', ['admin'], false],
        ['Billing', '#', ['admin', 'receptionist'], false],
        ['Reports', '#', ['admin'], false],
    ];
@endphp

<div class="flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden w-64 shrink-0 flex-col bg-slate-900 lg:flex">
        <a href="{{ route('home') }}" class="flex h-16 items-center gap-2.5 px-6">
            <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5"><path d="M12 5v14M5 12h14" /></svg>
            </span>
            <span class="font-bold text-white">{{ config('app.name') }}</span>
        </a>

        <nav class="mt-4 flex-1 space-y-1 px-3 text-sm font-medium">
            @foreach ($nav as [$label, $url, $roles, $ready])
                @if ($user->hasRole(...$roles))
                    @php $active = $ready && request()->url() === $url; @endphp
                    <a href="{{ $ready ? $url : '#' }}"
                       class="flex items-center justify-between rounded-lg px-3 py-2.5 transition {{ $active ? 'bg-brand-600 text-white' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }} {{ $ready ? '' : 'cursor-not-allowed opacity-60' }}">
                        {{ $label }}
                        @unless ($ready)
                            <span class="rounded-full bg-slate-700 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-slate-300">Soon</span>
                        @endunless
                    </a>
                @endif
            @endforeach
        </nav>

        <div class="border-t border-slate-800 p-4">
            <div class="flex items-center gap-3">
                <span class="flex size-10 items-center justify-center rounded-full bg-brand-600 font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-white">{{ $user->name }}</p>
                    <p class="text-xs text-brand-300">{{ $user->roleLabel() }}</p>
                </div>
            </div>
        </div>
    </aside>

    <div class="flex min-w-0 flex-1 flex-col">
        {{-- Top bar --}}
        <header class="flex h-16 items-center justify-between border-b border-slate-200 bg-white px-4 sm:px-8">
            <h1 class="text-lg font-semibold text-slate-900">@yield('title', 'Dashboard')</h1>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('home') }}" class="text-sm font-medium text-slate-600 hover:text-brand-600">View website</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-red-300 hover:text-red-600">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-8">
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>