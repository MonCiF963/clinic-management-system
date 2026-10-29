@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
@php
    $user = auth()->user();

    $cards = [
        ['Patients', 'Coming soon'],
        ['Appointments today', 'Coming soon'],
        ['Doctors on duty', 'Coming soon'],
        ['Revenue this month', 'Coming soon'],
    ];

    $todo = match ($user->role) {
        'admin' => ['Manage doctors and staff accounts', 'View reports and revenue', 'Oversee patients and billing'],
        'doctor' => ['See your daily appointments', 'Write consultation notes', 'Create prescriptions'],
        default => ['Register new patients', 'Book and manage appointments', 'Issue invoices and receipts'],
    };
@endphp

<div class="rounded-3xl bg-linear-to-br from-brand-600 to-brand-800 p-8 text-white shadow-xl shadow-brand-900/10">
    <p class="text-sm font-medium text-brand-100">{{ now()->format('l, F j, Y') }}</p>
    <h2 class="mt-2 text-3xl font-bold">Welcome back, {{ $user->name }}</h2>
    <p class="mt-2 text-brand-100">
        You are signed in as <span class="font-semibold text-white">{{ $user->roleLabel() }}</span>.
    </p>
</div>

<div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($cards as [$label, $value])
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="mt-3 text-lg font-semibold text-slate-400">{{ $value }}</p>
        </div>
    @endforeach
</div>

<div class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
    <h3 class="text-lg font-semibold text-slate-900">What you will be able to do</h3>
    <ul class="mt-4 space-y-3">
        @foreach ($todo as $item)
            <li class="flex items-center gap-3">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="size-5 shrink-0 text-brand-600"><path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                <span>{{ $item }}</span>
            </li>
        @endforeach
    </ul>
</div>
@endsection