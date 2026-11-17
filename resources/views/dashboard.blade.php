@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
@if (auth()->user()->hasRole('admin'))
    @include('dashboard-admin')
@else
@php
    $user = auth()->user();
    $tz = config('clinic.timezone');
    $isPatient = $user->hasRole('patient');
    $cards = [];
    $next = null;

    try {
        if ($isPatient) {
            $patientId = \App\Models\Patient::where('user_id', $user->id)->value('id');
            $next = \App\Models\Appointment::where('patient_id', $patientId ?? 0)->with(['doctor.user', 'service'])
                ->whereIn('status', ['pending', 'confirmed'])->where('start_at', '>', now())->orderBy('start_at')->first();
            $p = \App\Support\DashboardStats::patient((int) ($patientId ?? 0));
            $cards = [
                ['Upcoming appointments', number_format($p['upcoming'])],
                ['Completed visits', number_format($p['completed'])],
            ];
        } else {
            $cur = config('clinic.currency');
            $st = \App\Support\DashboardStats::staff($user->hasRole('admin'));
            $cards = [
                ['Patients', number_format($st['patients'])],
                ['Appointments today', number_format($st['today'])],
                ['Pending requests', number_format($st['pending'])],
                ['Doctors', number_format($st['doctors'])],
            ];
            if ($st['revenue'] !== null) {
                $cards[] = ['Revenue this month', number_format($st['revenue'], 2).' '.$cur];
            }
        }
    } catch (\Throwable $e) {
        report($e);
        $cards = [['Data', 'unavailable']];
    }
@endphp

<div class="rounded-3xl bg-linear-to-br from-brand-600 to-brand-800 p-8 text-white shadow-xl shadow-brand-900/10">
    <p class="text-sm font-medium text-brand-100">{{ now($tz)->format('l, F j, Y') }}</p>
    <h2 class="mt-2 text-3xl font-bold">Welcome back, {{ $user->name }}</h2>
    <p class="mt-2 text-brand-100">
        You are signed in as <span class="font-semibold text-white">{{ $user->roleLabel() }}</span>.
    </p>
    @if ($isPatient)
        <a href="{{ route('appointments.create') }}" class="hero-btn btn-shine mt-6 inline-flex rounded-full px-6 py-3 text-sm font-semibold">Book an appointment</a>
    @endif
</div>

<div class="mt-8 grid gap-5 sm:grid-cols-2 xl:grid-cols-4">
    @foreach ($cards as [$label, $value])
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
            <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
            <p class="stat-val mt-3 text-3xl font-bold text-slate-800">{{ $value }}</p>
        </div>
    @endforeach
</div>

@if ($isPatient)
    <div class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        <h3 class="text-lg font-semibold text-slate-900">Your next appointment</h3>
        @if ($next)
            <p class="mt-3 text-slate-700">
                <span class="font-semibold">{{ $next->start_at->timezone($tz)->format('l, F j \\a\\t H:i') }}</span>
                with {{ $next->doctor->user->name }} · {{ $next->service->name }}
                <span class="ml-2 rounded-full bg-amber-100 px-2.5 py-0.5 text-xs font-semibold text-amber-800">{{ ucfirst(str_replace('_', ' ', $next->status)) }}</span>
            </p>
        @else
            <p class="mt-3 text-slate-600">You have nothing booked. <a href="{{ route('appointments.create') }}" class="font-semibold text-brand-700 hover:underline">Pick a time</a>.</p>
        @endif
    </div>
@else
    <div class="mt-8 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        <h3 class="text-lg font-semibold text-slate-900">Quick links</h3>
        <div class="mt-4 flex flex-wrap gap-3">
            <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="rounded-full bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">Review pending requests</a>
            @if ($user->hasRole('admin'))
                <a href="{{ route('admin.doctors.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:border-brand-500 hover:text-brand-700">Manage doctors</a>
                <a href="{{ route('admin.services.index') }}" class="rounded-full border border-slate-300 px-5 py-2.5 text-sm font-semibold text-slate-700 hover:border-brand-500 hover:text-brand-700">Manage services</a>
            @endif
        </div>
    </div>
@endif
@endif
@endsection
