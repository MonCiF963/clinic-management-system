@extends('layouts.dashboard')

@section('title', auth()->user()->hasRole('patient') ? 'My appointments' : 'Appointments')

@section('content')
@php
    $user = auth()->user();
    $tz = config('clinic.timezone');
    $isStaff = $user->hasRole('admin', 'receptionist', 'doctor');
    $badge = [
        'pending' => 'bg-amber-100 text-amber-800',
        'confirmed' => 'bg-sky-100 text-sky-800',
        'checked_in' => 'bg-indigo-100 text-indigo-800',
        'completed' => 'bg-emerald-100 text-emerald-800',
        'cancelled' => 'bg-slate-200 text-slate-600',
        'no_show' => 'bg-red-100 text-red-700',
    ];
    $label = ['confirmed' => 'Confirm', 'checked_in' => 'Check in', 'completed' => 'Complete', 'cancelled' => 'Cancel', 'no_show' => 'No show'];
@endphp

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <form method="GET" class="flex items-center gap-2">
        <select name="status" onchange="this.form.requestSubmit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2 text-sm">
            <option value="">All statuses</option>
            @foreach ($statuses as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
    </form>
    @if ($user->hasRole('doctor'))
        <a href="{{ route('doctor.appointments.create') }}" class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">Book for a patient</a>
    @endif
    @if ($user->hasRole('patient'))
        <a href="{{ route('appointments.create') }}" class="rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-lg shadow-brand-600/25 transition hover:bg-brand-700">Book an appointment</a>
    @endif
</div>

<div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
    <table class="rtable w-full text-left text-sm">
        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
            <tr>
                <th class="px-5 py-3">When</th>
                @if ($isStaff)<th class="px-5 py-3">Patient</th>@endif
                <th class="px-5 py-3">Doctor</th>
                <th class="px-5 py-3">Service</th>
                <th class="px-5 py-3">Status</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($appointments as $a)
                <tr>
                    <td data-label="When" class="whitespace-nowrap px-5 py-4 font-medium text-slate-900">{{ $a->start_at->timezone($tz)->format('D, M j · H:i') }}</td>
                    @if ($isStaff)<td data-label="Patient" class="px-5 py-4">{{ $a->patient->user->name }}<br><span class="text-xs text-slate-500">{{ $a->patient->user->phone }}</span></td>@endif
                    <td data-label="Doctor" class="px-5 py-4">{{ $a->doctor->user->name }}</td>
                    <td data-label="Service" class="px-5 py-4">{{ $a->service->name }}</td>
                    <td data-label="Status" class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge[$a->status] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $a->status)) }}</span></td>
                    <td data-label="" class="actions px-5 py-4">
                        <div class="flex flex-wrap justify-end gap-2">
                            @if ($user->hasRole('patient') && in_array($a->status, ['pending', 'confirmed']) && $a->start_at->isFuture())
                                <form method="POST" action="{{ route('appointments.cancel', $a) }}" onsubmit="return confirm('Cancel this appointment?')">
                                    @csrf @method('PATCH')
                                    <button class="rounded-full border border-red-300 px-3 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">Cancel</button>
                                </form>
                            @endif
                            @if ($isStaff)
                                @foreach ($next[$a->status] ?? [] as $to)
                                    <form method="POST" action="{{ route('appointments.status', $a) }}">
                                        @csrf @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $to }}">
                                        <button class="rounded-full border px-3 py-1.5 text-xs font-semibold {{ in_array($to, ['cancelled', 'no_show']) ? 'border-red-300 text-red-600 hover:bg-red-50' : 'border-brand-300 text-brand-700 hover:bg-brand-50' }}">{{ $label[$to] }}</button>
                                    </form>
                                @endforeach
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">No appointments yet.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $appointments->links() }}</div>
@endsection
