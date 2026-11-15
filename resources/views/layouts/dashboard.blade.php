<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') – {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/dash.css') }}?v={{ filemtime(public_path('css/dash.css')) }}">
    <script src="{{ asset('js/dash.js') }}?v={{ filemtime(public_path('js/dash.js')) }}" defer></script>
</head>
<body class="dash font-sans antialiased">
<div id="dash-progress"></div>
<canvas id="hexbg"></canvas>
<div class="orb o1"></div><div class="orb o2"></div><div class="spot"></div>

@php
    $user = auth()->user();
    $all = ['admin', 'doctor', 'receptionist', 'patient'];
    $staff = ['admin', 'doctor', 'receptionist'];

    $icons = [
        'Dashboard' => '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>',
        'Book appointment' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18M12 14v4M10 16h4"/>',
        'Appointments' => '<rect x="3" y="4" width="18" height="17" rx="2"/><path d="M8 2v4M16 2v4M3 10h18"/>',
        'Doctors' => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'Services' => '<path d="M12 3v18M3 12h18"/>',
        'Specialties' => '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
        'Patients' => '<circle cx="9" cy="8" r="3.5"/><path d="M2 20a7 7 0 0 1 14 0M17 4a3.5 3.5 0 0 1 0 7M22 20a7 7 0 0 0-4-6"/>',
        'Billing' => '<rect x="2" y="5" width="20" height="14" rx="2"/><path d="M2 10h20"/>',
        'Reports' => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
    ];

    // [label, url, allowed roles, is the page built yet?]
    $nav = [
        ['Dashboard', route('dashboard'), $all, true],
        ['Book appointment', route('appointments.create'), ['patient'], true],
        ['Appointments', route('appointments.index'), $all, true],
        ['Doctors', route('admin.doctors.index'), ['admin'], true],
        ['Services', route('admin.services.index'), ['admin'], true],
        ['Specialties', route('admin.specialties.index'), ['admin'], true],
        ['Patients', '#', ['admin', 'receptionist'], false],
        ['Billing', '#', ['admin', 'receptionist'], false],
        ['Reports', '#', ['admin'], false],
    ];
@endphp

<div class="shell flex min-h-screen">

    {{-- Sidebar --}}
    <aside class="hidden w-64 shrink-0 flex-col lg:flex">
        <a href="{{ route('home') }}" class="flex h-16 items-center gap-2.5 px-6">
            <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5"><path d="M12 5v14M5 12h14" /></svg>
            </span>
            <span class="font-bold text-white">{{ config('app.name') }}</span>
        </a>

        <nav class="mt-4 flex-1 space-y-1 px-3 text-sm font-medium">
            @foreach ($nav as [$label, $url, $roles, $ready])
                @if ($user->hasRole(...$roles))
                    @php $active = $ready && (request()->url() === $url || str_starts_with(request()->url(), $url.'/')); @endphp
                    <a href="{{ $ready ? $url : '#' }}"
                       @if ($ready) data-nav @endif
                       class="flex items-center justify-between gap-3 px-3 py-2.5 {{ $active ? 'on' : '' }} {{ $ready ? '' : 'cursor-not-allowed opacity-60' }}">
                        <span class="flex items-center gap-3">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">{!! $icons[$label] ?? '' !!}</svg>
                            {{ $label }}
                        </span>
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
        <header class="top flex h-16 items-center justify-between border-b px-4 sm:px-8">
            <h1 id="page-title" class="text-lg font-semibold">@yield('title', 'Dashboard')</h1>

            <div class="flex items-center gap-2 sm:gap-4">
                <a href="{{ route('home') }}" data-full class="text-sm font-medium">View website</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="rounded-full border border-slate-300 px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-red-300 hover:text-red-600">
                        Log out
                    </button>
                </form>
            </div>
        </header>

        {{-- Mobile menu --}}
        <nav class="mnav flex gap-2 overflow-x-auto border-b px-4 py-2 text-sm font-medium lg:hidden">
            @foreach ($nav as [$label, $url, $roles, $ready])
                @if ($ready && $user->hasRole(...$roles))
                    <a href="{{ $url }}" data-nav class="shrink-0 rounded-full px-3.5 py-1.5 {{ request()->url() === $url ? 'on' : '' }}">{{ $label }}</a>
                @endif
            @endforeach
        </nav>

        <main id="page" class="flex-1 p-4 sm:p-8">
            @include('partials.flash')
            @yield('content')
        </main>
    </div>
</div>

</body>
</html>