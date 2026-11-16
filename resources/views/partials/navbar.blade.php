<header id="site-header" class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ url('/') }}" class="flex items-center gap-2.5">
            <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-lg shadow-brand-600/30">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5">
                    <path d="M12 5v14M5 12h14" />
                </svg>
            </span>
            <span class="text-lg font-bold tracking-tight text-slate-900">{{ config('app.name') }}</span>
        </a>

        {{-- Desktop links --}}
        <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
            <a href="{{ url('/') }}#services" class="transition hover:text-brand-600">Services</a>
            <a href="{{ url('/') }}#why-us" class="transition hover:text-brand-600">Why us</a>
            <a href="{{ url('/') }}#doctors" class="transition hover:text-brand-600">Doctors</a>
            <a href="{{ url('/') }}#reviews" class="transition hover:text-brand-600">Reviews</a>
            <a href="{{ url('/') }}#contact" class="transition hover:text-brand-600">Contact</a>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            @auth
                <a href="{{ route('dashboard') }}" class="px-3 py-2 text-sm font-semibold text-slate-700 transition hover:text-brand-600">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-slate-700 transition hover:text-brand-600">Sign in</a>
                <a href="{{ route('register') }}" class="px-3 py-2 text-sm font-semibold text-slate-700 transition hover:text-brand-600">Sign up</a>
            @endauth

            @auth
                @if (auth()->user()->hasRole('patient'))
                    <a href="{{ route('appointments.create') }}"
                       class="btn-shine rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                        Book appointment
                    </a>
                @endif
            @endauth
        </div>

        {{-- Phone: one clear action; navigation lives in the bottom tab bar --}}
        <div class="md:hidden">
            @auth
                <a href="{{ route('dashboard') }}" class="rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="rounded-full bg-brand-600 px-4 py-2 text-sm font-semibold text-white">Sign in</a>
            @endauth
        </div>
    </div>
</header>