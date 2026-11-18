@extends('layouts.dashboard')

@section('title', $user->name)

@section('content')
@php
    $tz = config('clinic.timezone');
    $badge = ['admin' => 'bg-brand-100 text-brand-800', 'doctor' => 'bg-sky-100 text-sky-800', 'receptionist' => 'bg-amber-100 text-amber-800', 'patient' => 'bg-emerald-100 text-emerald-800'];
    $status = ['pending' => 'bg-amber-100 text-amber-800', 'confirmed' => 'bg-brand-100 text-brand-800', 'checked_in' => 'bg-sky-100 text-sky-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-slate-200 text-slate-600', 'no_show' => 'bg-rose-100 text-rose-700'];
    $facts = [
        ['Email', $user->email],
        ['Phone', $user->phone ?: '—'],
        ['Language', $user->locale ?: '—'],
        ['Joined', $user->created_at?->timezone($tz)->format('M j, Y · H:i')],
        ['Last updated', $user->updated_at?->timezone($tz)->format('M j, Y · H:i')],
    ];
    if ($user->doctor) { $facts[] = ['Specialty', $user->doctor->specialty->name ?? '—']; }
    if ($user->patient) {
        $facts[] = ['Birth date', $user->patient->birth_date?->format('M j, Y') ?: '—'];
        $facts[] = ['Gender', $user->patient->gender ? ucfirst($user->patient->gender) : '—'];
        $facts[] = ['Blood type', $user->patient->blood_type ?: '—'];
    }
@endphp

<a href="{{ route('admin.users.index') }}" class="mb-5 inline-flex text-sm font-semibold text-brand-700 hover:underline">← All users</a>

<div class="grid gap-5 lg:grid-cols-3">
    <div class="v7-card in lg:col-span-1">
        <div class="flex items-center gap-4">
            <span class="v7-avatar" style="width:64px;height:64px;font-size:1.5rem;border-radius:22px">{{ strtoupper(mb_substr($user->name, 0, 1)) }}</span>
            <div class="min-w-0">
                <h2 class="truncate text-xl font-semibold text-slate-900">{{ $user->name }}</h2>
                <span class="mt-1 inline-block rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge[$user->role] ?? '' }}">{{ ucfirst($user->role) }}</span>
            </div>
        </div>
        <dl class="mt-6 space-y-4">
            @foreach ($facts as [$k, $v])
                <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 last:border-0">
                    <dt class="text-sm text-slate-500">{{ $k }}</dt>
                    <dd class="text-right text-sm font-medium text-slate-900 break-all">{{ $v }}</dd>
                </div>
            @endforeach
        </dl>
        @if ($user->patient && $user->patient->allergies)
            <p class="mt-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Allergies</p>
            <p class="mt-1 text-sm text-slate-700">{{ $user->patient->allergies }}</p>
        @endif
    </div>

    <div class="v7-card in lg:col-span-2">
        <h3 class="text-base font-semibold text-slate-900">Recent appointments</h3>
        @if ($appointments->isEmpty())
            <p class="mt-4 text-sm text-slate-500">No appointments for this account.</p>
        @else
            <div class="mt-4 overflow-x-auto">
                <table class="rtable w-full text-left text-sm">
                    <thead class="text-xs uppercase tracking-wide text-slate-500"><tr><th class="py-2 pr-4">When</th><th class="py-2 pr-4">{{ $user->patient ? 'Doctor' : 'Patient' }}</th><th class="py-2 pr-4">Service</th><th class="py-2">Status</th></tr></thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($appointments as $a)
                            <tr>
                                <td data-label="When" class="whitespace-nowrap py-3 pr-4 font-medium text-slate-900">{{ $a->start_at->timezone($tz)->format('M j, Y · H:i') }}</td>
                                <td data-label="{{ $user->patient ? 'Doctor' : 'Patient' }}" class="py-3 pr-4">{{ $user->patient ? $a->doctor->user->name : $a->patient->user->name }}</td>
                                <td data-label="Service" class="py-3 pr-4">{{ $a->service->name }}</td>
                                <td data-label="Status" class="py-3"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $status[$a->status] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $a->status)) }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
