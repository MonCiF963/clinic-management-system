@extends('layouts.app')

@section('title', config('app.name') . ' – Modern Healthcare, Close to You')
@section('description', 'Book appointments online, meet experienced doctors and get caring, modern healthcare for the whole family.')

@section('content')

@php
    $stats = [
        ['15+', 'Years of experience'],
        ['30k+', 'Happy patients'],
        ['35+', 'Medical specialists'],
        ['4.9/5', 'Average rating'],
    ];

    $services = [
        ['General Consultation', 'Thorough check-ups and accurate diagnosis from experienced family doctors.',
         'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
        ['Emergency Care', 'Rapid response and treatment whenever you need urgent medical attention.',
         'm3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z'],
        ['Cardiology', 'Heart health screening, monitoring and long-term treatment plans.',
         'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z'],
        ['Preventive Check-ups', 'Regular screenings that catch problems early and keep you healthy.',
         'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
        ['Lab & Diagnostics', 'Fast and reliable test results so your treatment can start sooner.',
         'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
        ['Online Appointments', 'Pick your doctor, date and time in a few clicks, without waiting in line.',
         'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
    ];

    $reasons = [
        'Experienced, board-certified doctors',
        'Modern equipment and spotless facilities',
        'Short waiting times and easy online booking',
        'Transparent pricing with no hidden fees',
        'Your medical records kept private and secure',
    ];

    $steps = [
        ['Book online', 'Choose your doctor and a time that suits you.'],
        ['Visit the clinic', 'Get a warm welcome and a thorough consultation.'],
        ['Get your care plan', 'Receive your diagnosis, prescription and follow-up.'],
    ];

    $doctors = [
        ['Dr. Samir Aouadi', 'Cardiologist', 'SA'],
        ['Dr. Nadia Hamidi', 'General Practitioner', 'NH'],
        ['Dr. Rayan Boukhalfa', 'Pediatrician', 'RB'],
        ['Dr. Meriem Saadi', 'Internal Medicine', 'MS'],
    ];

    $reviews = [
        ['Everyone was kind and professional, and I was seen right on time. Highly recommended.', 'Lydia T.', 'Patient'],
        ['Booking online was effortless. The doctor explained everything clearly and patiently.', 'Anis B.', 'Patient'],
        ['My kids actually enjoy coming here. Great pediatric care and a very friendly team.', 'Houda S.', 'Parent'],
    ];

    $avatarColors = ['bg-brand-300', 'bg-brand-400', 'bg-brand-500', 'bg-brand-600'];
@endphp

{{-- ============ HERO ============ --}}
<section class="relative overflow-hidden bg-linear-to-b from-brand-50 via-white to-white">
    <div class="pointer-events-none absolute -top-24 -right-24 size-96 rounded-full bg-brand-200/50 blur-3xl"></div>
    <div class="pointer-events-none absolute top-40 -left-24 size-72 rounded-full bg-brand-100/60 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-14 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-28">

        <div class="reveal">
            <span class="inline-flex items-center gap-2 rounded-full border border-brand-200 bg-white px-4 py-1.5 text-sm font-medium text-brand-700 shadow-sm">
                <span class="size-2 rounded-full bg-brand-500"></span>
                Now accepting new patients
            </span>

            <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-balance text-slate-900 sm:text-5xl lg:text-6xl">
                Healthcare that puts
                <span class="bg-linear-to-r from-brand-600 to-brand-400 bg-clip-text text-transparent">you first</span>
            </h1>

            <p class="mt-6 max-w-xl text-lg leading-8 text-slate-600">
                Experienced doctors, modern facilities and a caring team, all in one place.
                Book your appointment online in less than a minute.
            </p>

            <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                <a href="#contact"
                   class="inline-flex items-center justify-center rounded-full bg-brand-600 px-7 py-3.5 text-base font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:-translate-y-0.5 hover:bg-brand-700">
                    Book an appointment
                </a>
                <a href="#services"
                   class="inline-flex items-center justify-center rounded-full border border-slate-300 bg-white px-7 py-3.5 text-base font-semibold text-slate-800 transition hover:border-brand-400 hover:text-brand-700">
                    Explore services
                </a>
            </div>

            <div class="mt-10 flex items-center gap-4">
                <div class="flex -space-x-3">
                    @foreach (['A', 'B', 'C', 'D'] as $i => $letter)
                        <span class="flex size-10 items-center justify-center rounded-full border-2 border-white {{ $avatarColors[$i] }} text-sm font-bold text-white">{{ $letter }}</span>
                    @endforeach
                </div>
                <p class="text-sm text-slate-600">
                    <span class="font-semibold text-slate-900">30,000+</span> patients trust us
                    <span class="ml-1 text-amber-500">★★★★★</span>
                </p>
            </div>
        </div>

        {{-- Hero visual --}}
        <div class="reveal relative mx-auto w-full max-w-md lg:max-w-none">
            <div class="relative mx-auto max-w-md rounded-3xl bg-linear-to-br from-brand-600 to-brand-800 p-8 shadow-2xl shadow-brand-900/30">
                <div class="absolute inset-0 rounded-3xl bg-[radial-gradient(circle_at_30%_20%,rgba(255,255,255,0.25),transparent_50%)]"></div>

                <div class="relative rounded-2xl bg-white p-5 shadow-xl">
                    <p class="text-xs font-semibold tracking-wider text-brand-600 uppercase">Upcoming appointment</p>
                    <div class="mt-4 flex items-center gap-4">
                        <span class="flex size-14 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-700">SA</span>
                        <div>
                            <p class="font-semibold text-slate-900">Dr. Samir Aouadi</p>
                            <p class="text-sm text-slate-500">Cardiologist</p>
                        </div>
                    </div>
                    <div class="mt-5 grid grid-cols-2 gap-3 text-sm">
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-slate-500">Date</p>
                            <p class="font-semibold text-slate-900">Tomorrow</p>
                        </div>
                        <div class="rounded-xl bg-slate-50 p-3">
                            <p class="text-slate-500">Time</p>
                            <p class="font-semibold text-slate-900">10:30 AM</p>
                        </div>
                    </div>
                    <div class="mt-4 rounded-xl bg-brand-600 py-3 text-center text-sm font-semibold text-white">Confirmed ✓</div>
                </div>

                <div class="relative mt-6 grid grid-cols-2 gap-4 text-white">
                    <div class="rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <p class="text-3xl font-bold">98%</p>
                        <p class="text-sm text-brand-100">Patient satisfaction</p>
                    </div>
                    <div class="rounded-2xl bg-white/15 p-4 backdrop-blur">
                        <p class="text-3xl font-bold">24/7</p>
                        <p class="text-sm text-brand-100">Emergency care</p>
                    </div>
                </div>
            </div>

            {{-- Floating chip --}}
            <div class="animate-float absolute -top-5 -left-2 hidden items-center gap-3 rounded-2xl bg-white p-3 pr-5 shadow-xl ring-1 ring-slate-100 sm:flex lg:-left-8">
                <span class="flex size-10 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="size-5"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-semibold text-slate-900">Certified doctors</p>
                    <p class="text-xs text-slate-500">Licensed & verified</p>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============ STATS ============ --}}
<section class="border-y border-slate-200 bg-white">
    <div class="mx-auto grid max-w-7xl grid-cols-2 gap-8 px-4 py-12 sm:px-6 lg:grid-cols-4 lg:px-8">
        @foreach ($stats as [$number, $label])
            <div class="reveal text-center">
                <p class="text-3xl font-extrabold text-brand-600 sm:text-4xl">{{ $number }}</p>
                <p class="mt-1 text-sm font-medium text-slate-500">{{ $label }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============ SERVICES ============ --}}
<section id="services" class="scroll-mt-20 py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold tracking-wider text-brand-600 uppercase">Our services</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Complete care under one roof</h2>
            <p class="mt-4 text-lg text-slate-600">From routine check-ups to specialist treatment, we cover what you and your family need.</p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as [$title, $text, $icon])
                <article class="reveal group rounded-2xl border border-slate-200 bg-white p-7 transition hover:-translate-y-1 hover:border-brand-300 hover:shadow-xl hover:shadow-brand-600/10">
                    <span class="flex size-12 items-center justify-center rounded-xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-6">
                            <path d="{{ $icon }}" />
                        </svg>
                    </span>
                    <h3 class="mt-5 text-lg font-semibold text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 text-sm leading-6 text-slate-600">{{ $text }}</p>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ WHY US ============ --}}
<section id="why-us" class="scroll-mt-20 bg-slate-50 py-20 sm:py-28">
    <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">

        <div class="reveal">
            <p class="text-sm font-semibold tracking-wider text-brand-600 uppercase">Why choose us</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Care you can trust, every single visit</h2>
            <p class="mt-4 text-lg text-slate-600">
                We combine medical expertise with a human touch, so you always feel heard, informed and looked after.
            </p>

            <ul class="mt-8 space-y-4">
                @foreach ($reasons as $reason)
                    <li class="flex items-start gap-3">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 size-6 shrink-0 text-brand-600"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        <span class="font-medium text-slate-700">{{ $reason }}</span>
                    </li>
                @endforeach
            </ul>
        </div>

        {{-- How it works --}}
        <div class="reveal rounded-3xl bg-white p-8 shadow-xl shadow-slate-200/60 ring-1 ring-slate-100 sm:p-10">
            <h3 class="text-xl font-bold text-slate-900">How it works</h3>
            <ol class="mt-8 space-y-8">
                @foreach ($steps as $i => [$title, $text])
                    <li class="flex gap-5">
                        <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-brand-600 text-lg font-bold text-white shadow-lg shadow-brand-600/30">{{ $i + 1 }}</span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $title }}</p>
                            <p class="mt-1 text-sm text-slate-600">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>

{{-- ============ DOCTORS ============ --}}
<section id="doctors" class="scroll-mt-20 py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold tracking-wider text-brand-600 uppercase">Our team</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-slate-900 sm:text-4xl">Meet our doctors</h2>
            <p class="mt-4 text-lg text-slate-600">Skilled specialists who listen first and treat second.</p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($doctors as [$name, $specialty, $initials])
                <article class="reveal overflow-hidden rounded-2xl border border-slate-200 bg-white text-center transition hover:-translate-y-1 hover:shadow-xl">
                    <div class="flex h-44 items-center justify-center bg-linear-to-br from-brand-100 to-brand-200">
                        <span class="flex size-24 items-center justify-center rounded-full bg-white text-3xl font-bold text-brand-700 shadow-lg">{{ $initials }}</span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-semibold text-slate-900">{{ $name }}</h3>
                        <p class="mt-1 text-sm text-brand-600">{{ $specialty }}</p>
                        <a href="#contact" class="mt-4 inline-block text-sm font-semibold text-slate-700 hover:text-brand-600">Book a visit →</a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ REVIEWS ============ --}}
<section id="reviews" class="scroll-mt-20 bg-brand-900 py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="reveal mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold tracking-wider text-brand-300 uppercase">Testimonials</p>
            <h2 class="mt-3 text-3xl font-bold tracking-tight text-white sm:text-4xl">What our patients say</h2>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach ($reviews as [$quote, $author, $role])
                <figure class="reveal rounded-2xl bg-white/5 p-7 ring-1 ring-white/10 backdrop-blur">
                    <p class="text-amber-400">★★★★★</p>
                    <blockquote class="mt-4 leading-7 text-brand-50">“{{ $quote }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3">
                        <span class="flex size-10 items-center justify-center rounded-full bg-brand-500 text-sm font-bold text-white">{{ substr($author, 0, 1) }}</span>
                        <div>
                            <p class="text-sm font-semibold text-white">{{ $author }}</p>
                            <p class="text-xs text-brand-300">{{ $role }}</p>
                        </div>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CONTACT / APPOINTMENT ============ --}}
<section id="contact" class="scroll-mt-20 py-20 sm:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="reveal grid overflow-hidden rounded-3xl bg-linear-to-br from-brand-600 to-brand-800 shadow-2xl shadow-brand-900/20 lg:grid-cols-2">

            <div class="p-8 text-white sm:p-12">
                <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Book your appointment today</h2>
                <p class="mt-4 text-lg text-brand-100">Fill in the form and our team will contact you shortly to confirm your visit.</p>

                <ul class="mt-8 space-y-4 text-brand-50">
                    <li class="flex items-center gap-3">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-6"><path d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 0 0 2.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 0 1-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 0 0-1.091-.852H4.5A2.25 2.25 0 0 0 2.25 4.5v2.25Z"/></svg>
                        +1 (555) 010-2030
                    </li>
                    <li class="flex items-center gap-3">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="size-6"><path d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        Sun – Thu, 08:00 – 18:00
                    </li>
                </ul>
            </div>

            {{-- UI only for now: we will connect it to the database in the appointments step --}}
            <form class="space-y-4 bg-white p-8 sm:p-12" onsubmit="return false;">
                <div class="grid gap-4 sm:grid-cols-2">
                    <input type="text" placeholder="Full name" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                    <input type="tel" placeholder="Phone number" required
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <select class="w-full rounded-xl border border-slate-300 bg-white px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                        <option>General Consultation</option>
                        <option>Cardiology</option>
                        <option>Pediatrics</option>
                        <option>Internal Medicine</option>
                    </select>
                    <input type="date"
                           class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>
                <textarea rows="4" placeholder="Tell us briefly how we can help (optional)"
                          class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200"></textarea>
                <button type="submit"
                        class="w-full rounded-xl bg-brand-600 py-3.5 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">
                    Request appointment
                </button>
            </form>
        </div>
    </div>
</section>

@endsection