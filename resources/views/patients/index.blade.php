@extends('layouts.dashboard')

@section('title', 'Patients')

@section('content')
@php $tz = config('clinic.timezone'); @endphp

<form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
    <input type="search" name="q" value="{{ $q }}" placeholder="Search name, email or phone" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm sm:w-80">
    <button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white">Search</button>
    @if ($q !== '')<a href="{{ route('patients.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">Clear</a>@endif
    <span class="ml-auto text-sm text-slate-500">{{ $patients->total() }} patient{{ $patients->total() === 1 ? '' : 's' }}</span>
</form>

<div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
    <table class="rtable w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Patient</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Visits</th><th class="px-5 py-3">Last / next visit</th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($patients as $p)
                <tr>
                    <td data-label="Patient" class="px-5 py-4">
                        <span class="flex items-center gap-3"><span class="v7-avatar">{{ strtoupper(mb_substr($p->user->name, 0, 1)) }}</span><span class="font-medium text-slate-900">{{ $p->user->name }}</span></span>
                    </td>
                    <td data-label="Email" class="px-5 py-4">{{ $p->user->email }}</td>
                    <td data-label="Phone" class="px-5 py-4">{{ $p->user->phone ?: '—' }}</td>
                    <td data-label="Visits" class="px-5 py-4">{{ $p->appointments_count }}</td>
                    <td data-label="Last / next" class="whitespace-nowrap px-5 py-4">{{ $p->appointments_max_start_at ? \Carbon\Carbon::parse($p->appointments_max_start_at)->timezone($tz)->format('M j, Y · H:i') : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-5 py-12 text-center text-slate-500">No patients found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $patients->links() }}</div>
@endsection
