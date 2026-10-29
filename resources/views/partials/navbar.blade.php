<header class="sticky top-0 z-50 border-b border-slate-200/70 bg-white/80 backdrop-blur-lg">
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
                <a href="{{ route('login') }}" class="px-3 py-2 text-sm font-semibold text-slate-700 transition hover:text-brand-600">Staff login</a>
            @endauth

            <a href="{{ url('/') }}#contact"
               class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                Book appointment
            </a>
        </div>

        {{-- Mobile button --}}
        <button id="menu-button" type="button" aria-expanded="false" aria-label="Toggle menu"
                class="inline-flex size-10 items-center justify-center rounded-lg text-slate-700 hover:bg-slate-100 md:hidden">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" class="size-6">
                <path d="M4 7h16M4 12h16M4 17h16" />
            </svg>
        </button>
    </div>

    {{-- Mobile menu --}}
    <div id="mobile-menu" class="hidden border-t border-slate-200 bg-white md:hidden">
        <div class="space-y-1 px-4 py-4 text-sm font-medium">
            <a href="{{ url('/') }}#services" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Services</a>
            <a href="{{ url('/') }}#why-us" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Why us</a>
            <a href="{{ url('/') }}#doctors" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Doctors</a>
            <a href="{{ url('/') }}#reviews" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Reviews</a>
            <a href="{{ url('/') }}#contact" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Contact</a>

            @auth
                <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block rounded-lg px-3 py-2.5 hover:bg-slate-50">Staff login</a>
            @endauth

            <a href="{{ url('/') }}#contact"
               class="mt-3 block rounded-full bg-brand-600 px-5 py-3 text-center font-semibold text-white">
                Book appointment
            </a>
        </div>
    </div>
</header>