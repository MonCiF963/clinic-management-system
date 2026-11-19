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
            <a href="{{ url('/') }}#top" class="transition hover:text-brand-600">Home</a>
            <a href="{{ url('/') }}#services" class="transition hover:text-brand-600">Services</a>
            <a href="{{ url('/') }}#why-us" class="transition hover:text-brand-600">Why us</a>
            <a href="{{ url('/') }}#doctors" class="transition hover:text-brand-600">Doctors</a>
            <a href="{{ url('/') }}#reviews" class="transition hover:text-brand-600">Reviews</a>
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

        {{-- Phone: menu button, opens the same links/buttons as the desktop bar --}}
        <button type="button" id="menu-btn" class="menu-btn md:hidden" aria-label="Open menu" aria-expanded="false" aria-controls="mobile-menu">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><path class="l1" d="M4 7h16"/><path class="l2" d="M4 12h16"/><path class="l3" d="M4 17h16"/></svg>
        </button>
    </div>

    <div id="mobile-menu" class="mobile-menu md:hidden" hidden>
        <nav class="mm-links">
            <a href="{{ url('/') }}#top">Home</a>
            <a href="{{ url('/') }}#services">Services</a>
            <a href="{{ url('/') }}#why-us">Why us</a>
            <a href="{{ url('/') }}#doctors">Doctors</a>
            <a href="{{ url('/') }}#reviews">Reviews</a>
        </nav>
        <div class="mm-actions">
            @auth
                <a href="{{ route('dashboard') }}" class="mm-ghost">Dashboard</a>
                @if (auth()->user()->hasRole('patient'))
                    <a href="{{ route('appointments.create') }}" class="mm-primary bg-brand-600 btn-shine">Book appointment</a>
                @endif
            @else
                <a href="{{ route('login') }}" class="mm-ghost">Sign in</a>
                <a href="{{ route('register') }}" class="mm-ghost">Sign up</a>
            @endauth
        </div>
    </div>
</header>