@extends('layouts.dashboard')

@section('title', 'Book for a patient')

@section('content')
@php $tz = config('clinic.timezone'); @endphp

<div class="mx-auto max-w-3xl space-y-8">
    <form method="GET" action="{{ route('doctor.appointments.create') }}" class="space-y-5 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
        <h2 class="text-lg font-semibold text-slate-900">1. Choose your patient, a service and a day</h2>

        @if ($patients->isEmpty())
            <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">You have no patients yet. Patients appear here once they have booked with you.</p>
        @elseif ($services->isEmpty())
            <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">No active services yet. Ask the admin to add services.</p>
        @else
            <div class="grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-slate-700" for="patient_id">Patient</label>
                    <select id="patient_id" name="patient_id" required class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                        <option value="">Select…</option>
                        @foreach ($patients as $p)
                            <option value="{{ $p->id }}" @selected((string) request('patient_id') === (string) $p->id)>{{ $p->user->name }}@if ($p->user->phone) · {{ $p->user->phone }}@endif</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="service_id">Service</label>
                    <select id="service_id" name="service_id" required class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                        <option value="">Select…</option>
                        @foreach ($services as $s)
                            <option value="{{ $s->id }}" @selected((string) request('service_id') === (string) $s->id)>{{ $s->name }} ({{ $s->duration_minutes }} min)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700" for="date">Date</label>
                    <input id="date" type="date" name="date" required min="{{ $today }}" max="{{ $maxDate }}" value="{{ request('date') }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-4 py-3 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                </div>
            </div>
            <button type="submit" class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">Show my free times</button>
        @endif
    </form>

    @if ($chosen)
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:p-8">
            <h2 class="text-lg font-semibold text-slate-900">2. Pick a time</h2>
            <p class="mt-1 text-sm text-slate-500">
                {{ $chosen['service']->name }} for {{ $chosen['patient']->user->name }} on
                {{ \Carbon\Carbon::parse($chosen['date'])->format('l, F j') }} ({{ $tz }} time)
            </p>

            @if ($slots->isEmpty())
                <p class="mt-5 rounded-xl bg-slate-50 px-4 py-3 text-sm text-slate-600">No free times that day. Try another day. Check your working hours with the admin if this is unexpected.</p>
            @else
                <div class="mt-5 grid grid-cols-3 gap-3 sm:grid-cols-4">
                    @foreach ($slots as $slot)
                        <form method="POST" action="{{ route('doctor.appointments.store') }}">
                            @csrf
                            <input type="hidden" name="patient_id" value="{{ $chosen['patient']->id }}">
                            <input type="hidden" name="service_id" value="{{ $chosen['service']->id }}">
                            <input type="hidden" name="start" value="{{ $slot->format('Y-m-d H:i') }}">
                            <button type="submit" class="w-full rounded-xl border border-brand-300 py-2.5 text-sm font-semibold text-brand-700 transition hover:bg-brand-600 hover:text-white">{{ $slot->format('H:i') }}</button>
                        </form>
                    @endforeach
                </div>
                <p class="mt-4 text-xs text-slate-500">Clicking a time books it as confirmed.</p>
            @endif
        </div>
    @endif
</div>
@endsection
