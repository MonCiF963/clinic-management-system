{{-- Phone-only bottom navigation for the public site --}}
@php
    $home = url('/');
    $tabs = [
        ['Home', $home.'#top', 'M3 11.5 12 4l9 7.5V20a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z'],
        ['Services', $home.'#services', 'M12 3v18M3 12h18'],
        ['Doctors', $home.'#doctors', 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0'],
    ];
@endphp
<nav class="tabbar md:hidden" aria-label="Main">
    @foreach ($tabs as [$label, $href, $d])
        <a href="{{ $href }}" data-tab="{{ strtolower($label) }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $d }}"/></svg>
            <span>{{ $label }}</span>
        </a>
    @endforeach
    @auth
        <a href="{{ route('dashboard') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>Account</span></a>
    @else
        <a href="{{ route('register') }}"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"><circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/></svg><span>Sign up</span></a>
    @endauth
</nav>
