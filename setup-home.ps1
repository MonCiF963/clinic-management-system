function Write-File($path, $content) {
    $dir = Split-Path $path -Parent
    if ($dir) { New-Item -ItemType Directory -Force -Path $dir | Out-Null }
    $full = Join-Path (Get-Location) $path
    [System.IO.File]::WriteAllText($full, $content, (New-Object System.Text.UTF8Encoding($false)))
    Write-Host "Created $path"
}

# ---------- CSS ----------
Write-File 'resources/css/app.css' @'
@import 'tailwindcss';

@source '../../vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php';
@source '../../storage/framework/views/*.php';

@theme {
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
        'Segoe UI Symbol', 'Noto Color Emoji';

    --color-brand-50: #f0fdfa;
    --color-brand-100: #ccfbf1;
    --color-brand-200: #99f6e4;
    --color-brand-500: #14b8a6;
    --color-brand-600: #0d9488;
    --color-brand-700: #0f766e;
    --color-brand-900: #134e4a;

    --animate-float: float 5s ease-in-out infinite;

    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
}
'@

# ---------- JS ----------
Write-File 'resources/js/app.js' @'
const button = document.getElementById('menu-button');
const menu = document.getElementById('mobile-menu');

button?.addEventListener('click', () => menu.classList.toggle('hidden'));

menu?.querySelectorAll('a').forEach((link) =>
    link.addEventListener('click', () => menu.classList.add('hidden'))
);
'@

# ---------- Controller + Route ----------
Write-File 'app/Http/Controllers/HomeController.php' @'
<?php

namespace App\Http\Controllers;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }
}
'@

Write-File 'routes/web.php' @'
<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
'@

# ---------- Layout ----------
Write-File 'resources/views/layouts/app.blade.php' @'
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', config('app.name'))</title>
    <meta name="description" content="@yield('description', 'Modern, caring healthcare for you and your family.')">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white font-sans text-slate-600 antialiased">

    @include('partials.navbar')

    <main>
        @yield('content')
    </main>

    @include('partials.footer')

</body>
</html>
'@

# ---------- Navbar ----------
Write-File 'resources/views/partials/navbar.blade.php' @'
<header class="sticky top-0 z-50 border-b border-slate-100 bg-white/80 backdrop-blur-lg">
    <div class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">

        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white shadow-md shadow-brand-600/30">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                </svg>
            </span>
            <span class="text-lg font-bold text-slate-900">Medi<span class="text-brand-600">Care</span></span>
        </a>

        <nav class="hidden items-center gap-8 text-sm font-medium md:flex">
            <a href="#home" class="transition hover:text-brand-600">Home</a>
            <a href="#services" class="transition hover:text-brand-600">Services</a>
            <a href="#about" class="transition hover:text-brand-600">About</a>
            <a href="#doctors" class="transition hover:text-brand-600">Doctors</a>
            <a href="#contact" class="transition hover:text-brand-600">Contact</a>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            <a href="#" class="text-sm font-semibold text-slate-700 transition hover:text-brand-600">Log in</a>
            <a href="#appointment"
               class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white shadow-md shadow-brand-600/30 transition hover:-translate-y-0.5 hover:bg-brand-700">
                Book Appointment
            </a>
        </div>

        <button id="menu-button" type="button" aria-label="Open menu"
                class="rounded-lg p-2 text-slate-700 hover:bg-slate-100 md:hidden">
            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>

    <div id="mobile-menu" class="hidden border-t border-slate-100 bg-white px-4 py-4 md:hidden">
        <div class="flex flex-col gap-1 text-sm font-medium">
            <a href="#home" class="rounded-lg px-3 py-2 hover:bg-slate-50">Home</a>
            <a href="#services" class="rounded-lg px-3 py-2 hover:bg-slate-50">Services</a>
            <a href="#about" class="rounded-lg px-3 py-2 hover:bg-slate-50">About</a>
            <a href="#doctors" class="rounded-lg px-3 py-2 hover:bg-slate-50">Doctors</a>
            <a href="#contact" class="rounded-lg px-3 py-2 hover:bg-slate-50">Contact</a>
            <a href="#appointment" class="mt-2 rounded-full bg-brand-600 px-5 py-2.5 text-center font-semibold text-white">
                Book Appointment
            </a>
        </div>
    </div>
</header>
'@

# ---------- Footer ----------
Write-File 'resources/views/partials/footer.blade.php' @'
<footer id="contact" class="bg-slate-900 text-slate-400">
    <div class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 md:grid-cols-4 lg:px-8">

        <div class="md:col-span-1">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 5v14M5 12h14" />
                    </svg>
                </span>
                <span class="text-lg font-bold text-white">Medi<span class="text-brand-500">Care</span></span>
            </a>
            <p class="mt-4 text-sm leading-relaxed">
                Modern, compassionate healthcare for the whole family, all under one roof.
            </p>
        </div>

        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-white">Quick links</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li><a href="#home" class="hover:text-white">Home</a></li>
                <li><a href="#services" class="hover:text-white">Services</a></li>
                <li><a href="#doctors" class="hover:text-white">Our doctors</a></li>
                <li><a href="#appointment" class="hover:text-white">Book appointment</a></li>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-white">Opening hours</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li>Sun – Thu: 08:00 – 18:00</li>
                <li>Friday: Closed</li>
                <li>Saturday: 09:00 – 14:00</li>
                <li class="font-medium text-brand-500">Emergency: 24/7</li>
            </ul>
        </div>

        <div>
            <h4 class="text-sm font-semibold uppercase tracking-wider text-white">Contact</h4>
            <ul class="mt-4 space-y-2 text-sm">
                <li>123 Health Street, Your City</li>
                <li>+000 000 000 000</li>
                <li>contact@medicare.com</li>
            </ul>
        </div>
    </div>

    <div class="border-t border-slate-800 py-6 text-center text-sm">
        © {{ date('Y') }} MediCare Clinic. All rights reserved.
    </div>
</footer>
'@

# ---------- Home ----------
Write-File 'resources/views/home.blade.php' @'
@extends('layouts.app')

@section('title', 'MediCare Clinic | Modern healthcare for your family')

@section('content')
    @include('sections.hero')
    @include('sections.services')
    @include('sections.why-us')
    @include('sections.doctors')
    @include('sections.testimonials')
    @include('sections.appointment')
@endsection
'@

# ---------- Hero ----------
Write-File 'resources/views/sections/hero.blade.php' @'
<section id="home" class="relative overflow-hidden bg-linear-to-b from-brand-50 via-white to-white">
    <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-brand-200/50 blur-3xl"></div>
    <div class="absolute top-40 -left-24 h-72 w-72 rounded-full bg-sky-200/40 blur-3xl"></div>

    <div class="relative mx-auto grid max-w-7xl items-center gap-12 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">

        <div>
            <span class="inline-flex items-center gap-2 rounded-full bg-brand-100 px-4 py-1.5 text-sm font-semibold text-brand-700">
                <span class="h-2 w-2 animate-pulse rounded-full bg-brand-600"></span>
                Now accepting new patients
            </span>

            <h1 class="mt-6 text-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
                Your health,
                <span class="bg-linear-to-r from-brand-600 to-sky-500 bg-clip-text text-transparent">our priority.</span>
            </h1>

            <p class="mt-6 max-w-xl text-lg leading-relaxed">
                Expert doctors, modern equipment and a caring team. Book your visit online
                in under a minute and skip the waiting room.
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
                <a href="#appointment"
                   class="rounded-full bg-brand-600 px-7 py-3.5 font-semibold text-white shadow-lg shadow-brand-600/30 transition hover:-translate-y-0.5 hover:bg-brand-700">
                    Book Appointment
                </a>
                <a href="#services"
                   class="rounded-full border border-slate-200 bg-white px-7 py-3.5 font-semibold text-slate-700 transition hover:-translate-y-0.5 hover:border-brand-600 hover:text-brand-600">
                    Our Services
                </a>
            </div>

            <dl class="mt-12 grid max-w-md grid-cols-3 gap-6">
                <div>
                    <dt class="sr-only">Years of experience</dt>
                    <dd class="text-3xl font-extrabold text-slate-900">15+</dd>
                    <p class="text-sm">Years experience</p>
                </div>
                <div>
                    <dt class="sr-only">Happy patients</dt>
                    <dd class="text-3xl font-extrabold text-slate-900">12k</dd>
                    <p class="text-sm">Happy patients</p>
                </div>
                <div>
                    <dt class="sr-only">Specialists</dt>
                    <dd class="text-3xl font-extrabold text-slate-900">40+</dd>
                    <p class="text-sm">Specialists</p>
                </div>
            </dl>
        </div>

        <div class="relative mx-auto w-full max-w-md">
            <div class="absolute inset-0 rotate-3 rounded-[2.5rem] bg-linear-to-br from-brand-500 to-sky-500 opacity-20"></div>

            <div class="relative rounded-[2.5rem] bg-linear-to-br from-brand-600 to-brand-900 p-8 shadow-2xl shadow-brand-900/30">
                <p class="text-sm font-medium text-brand-100">Next appointment</p>

                <div class="mt-4 rounded-2xl bg-white p-5 shadow-lg">
                    <div class="flex items-center gap-4">
                        <div class="flex h-14 w-14 items-center justify-center rounded-full bg-linear-to-br from-brand-500 to-sky-500 text-lg font-bold text-white">
                            DR
                        </div>
                        <div>
                            <p class="font-semibold text-slate-900">Dr. Sarah Ahmed</p>
                            <p class="text-sm">Cardiologist</p>
                        </div>
                        <span class="ml-auto rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-700">Confirmed</span>
                    </div>
                    <div class="mt-4 flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3 text-sm">
                        <span>Tomorrow</span>
                        <span class="font-semibold text-slate-900">10:30 AM</span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-4 text-white">
                    <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                        <p class="text-2xl font-bold">98%</p>
                        <p class="text-xs text-brand-100">Patient satisfaction</p>
                    </div>
                    <div class="rounded-2xl bg-white/10 p-4 backdrop-blur">
                        <p class="text-2xl font-bold">24/7</p>
                        <p class="text-xs text-brand-100">Emergency care</p>
                    </div>
                </div>
            </div>

            <div class="absolute -bottom-6 -left-6 animate-float rounded-2xl bg-white p-4 shadow-xl">
                <div class="flex items-center gap-3">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-rose-100 text-rose-500">
                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21s-8-4.8-8-11a4.5 4.5 0 0 1 8-2.8A4.5 4.5 0 0 1 20 10c0 6.2-8 11-8 11Z"/></svg>
                    </span>
                    <div>
                        <p class="text-sm font-semibold text-slate-900">Heart rate</p>
                        <p class="text-xs">72 bpm · Normal</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
'@

# ---------- Services ----------
Write-File 'resources/views/sections/services.blade.php' @'
@php
    $services = [
        ['General Checkup', 'Complete health examinations to catch problems early.', 'M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z'],
        ['Cardiology', 'Advanced heart care, from screening to long-term treatment.', 'M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z'],
        ['Laboratory Tests', 'Fast, accurate lab results with secure online access.', 'M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 0-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5'],
        ['Family Medicine', 'Caring treatment for every member of your family.', 'M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z'],
        ['Online Booking', 'Choose your doctor and time slot from anywhere.', 'M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5'],
        ['Emergency Care', 'Round-the-clock urgent care when you need it most.', 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z'],
    ];
@endphp

<section id="services" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">What we offer</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Complete care, one clinic</h2>
            <p class="mt-4">From routine checkups to specialist treatment, we cover your health needs with modern technology and a human touch.</p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($services as [$title, $text, $icon])
                <div class="group rounded-3xl border border-slate-100 bg-white p-8 transition duration-300 hover:-translate-y-2 hover:border-brand-200 hover:shadow-xl hover:shadow-brand-600/10">
                    <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-brand-50 text-brand-600 transition group-hover:bg-brand-600 group-hover:text-white">
                        <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $icon }}" />
                        </svg>
                    </span>
                    <h3 class="mt-6 text-xl font-bold text-slate-900">{{ $title }}</h3>
                    <p class="mt-2 leading-relaxed">{{ $text }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
'@

# ---------- Why us ----------
Write-File 'resources/views/sections/why-us.blade.php' @'
<section id="about" class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Why choose us</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Care built around you</h2>
            <p class="mt-4 leading-relaxed">
                We combine experienced doctors with a simple digital experience, so your visit is
                smooth from the first click to the follow-up.
            </p>

            <ul class="mt-8 space-y-5">
                @foreach ([
                    ['Experienced specialists', 'Qualified doctors across many medical fields.'],
                    ['Modern equipment', 'Up-to-date diagnostic tools for accurate results.'],
                    ['Short waiting times', 'Online scheduling means less time in the waiting room.'],
                    ['Private & secure records', 'Your medical information is protected and confidential.'],
                ] as [$title, $text])
                    <li class="flex gap-4">
                        <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $title }}</p>
                            <p class="text-sm">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-[2.5rem] bg-white p-8 shadow-xl shadow-slate-200/60 sm:p-10">
            <h3 class="text-xl font-bold text-slate-900">How it works</h3>
            <ol class="mt-8 space-y-8">
                @foreach ([
                    ['Choose your doctor', 'Browse specialists and pick the right one.'],
                    ['Pick a date & time', 'Select a slot that fits your schedule.'],
                    ['Get treated', 'Arrive, get seen on time, and receive your care plan.'],
                ] as $i => [$title, $text])
                    <li class="flex gap-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-700 text-lg font-bold text-white">
                            {{ $i + 1 }}
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $title }}</p>
                            <p class="text-sm">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>
'@

# ---------- Doctors ----------
Write-File 'resources/views/sections/doctors.blade.php' @'
@php
    $doctors = [
        ['Dr. Sarah Ahmed', 'Cardiologist', 'SA', 'from-brand-500 to-sky-500'],
        ['Dr. Karim Benali', 'General Practitioner', 'KB', 'from-sky-500 to-indigo-500'],
        ['Dr. Lina Haddad', 'Pediatrician', 'LH', 'from-pink-500 to-rose-500'],
        ['Dr. Omar Saidi', 'Dermatologist', 'OS', 'from-amber-500 to-orange-500'],
    ];
@endphp

<section id="doctors" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Our team</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Meet our doctors</h2>
            <p class="mt-4">Experienced, friendly professionals dedicated to your wellbeing.</p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($doctors as [$name, $specialty, $initials, $gradient])
                <div class="group overflow-hidden rounded-3xl border border-slate-100 bg-white text-center transition duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-slate-200/70">
                    <div class="bg-linear-to-br {{ $gradient }} flex h-40 items-center justify-center">
                        <span class="flex h-24 w-24 items-center justify-center rounded-full bg-white/20 text-3xl font-bold text-white ring-4 ring-white/40">
                            {{ $initials }}
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-slate-900">{{ $name }}</h3>
                        <p class="text-sm text-brand-600">{{ $specialty }}</p>
                        <a href="#appointment" class="mt-4 inline-block rounded-full bg-slate-100 px-5 py-2 text-sm font-semibold text-slate-700 transition group-hover:bg-brand-600 group-hover:text-white">
                            Book now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
'@

# ---------- Testimonials ----------
Write-File 'resources/views/sections/testimonials.blade.php' @'
<section class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Testimonials</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Loved by our patients</h2>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['Booking was so easy and the doctor was kind and professional. Highly recommended!', 'Amina K.', 'Patient'],
                ['No long waiting. I was seen on time and everything was explained clearly.', 'Yacine M.', 'Patient'],
                ['The whole family goes here now. The pediatrician is wonderful with our kids.', 'Nadia B.', 'Parent'],
            ] as [$quote, $name, $role])
                <figure class="rounded-3xl bg-white p-8 shadow-sm">
                    <div class="flex gap-1 text-amber-400">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1.5l2.7 5.5 6 .9-4.4 4.2 1 6-5.3-2.8-5.3 2.8 1-6L1.3 7.9l6-.9L10 1.5Z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="mt-4 leading-relaxed">“{{ $quote }}”</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 font-bold text-brand-700">
                            {{ substr($name, 0, 1) }}
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">{{ $name }}</span>
                            <span class="block text-xs">{{ $role }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>
'@

# ---------- Appointment ----------
Write-File 'resources/views/sections/appointment.blade.php' @'
<section id="appointment" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-brand-600 to-brand-900 px-6 py-14 sm:px-12 lg:px-16">
            <div class="absolute -top-20 -right-20 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>

            <div class="relative grid items-center gap-12 lg:grid-cols-2">
                <div class="text-white">
                    <h2 class="text-3xl font-extrabold sm:text-4xl">Book your appointment today</h2>
                    <p class="mt-4 text-brand-100">
                        Fill in the form and our team will confirm your visit shortly.
                        For emergencies, call us any time, day or night.
                    </p>
                    <p class="mt-8 text-sm text-brand-100">Emergency line</p>
                    <p class="text-2xl font-bold">+000 000 000 000</p>
                </div>

                <form action="#" method="get" class="grid gap-4 rounded-3xl bg-white p-6 shadow-2xl sm:grid-cols-2 sm:p-8">
                    <input type="text" placeholder="Full name" required
                           class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20">
                    <input type="tel" placeholder="Phone number" required
                           class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20">
                    <select class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20">
                        <option>General Checkup</option>
                        <option>Cardiology</option>
                        <option>Pediatrics</option>
                        <option>Dermatology</option>
                    </select>
                    <input type="date" required
                           class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20">
                    <textarea rows="3" placeholder="Tell us briefly how we can help (optional)"
                              class="rounded-xl border border-slate-200 px-4 py-3 text-sm outline-none focus:border-brand-600 focus:ring-2 focus:ring-brand-600/20 sm:col-span-2"></textarea>
                    <button type="submit"
                            class="rounded-xl bg-brand-600 px-6 py-3.5 font-semibold text-white transition hover:bg-brand-700 sm:col-span-2">
                        Request Appointment
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>
'@

Write-Host ""
Write-Host "All files created. Now run: npm run dev  and  php artisan serve" -ForegroundColor Green