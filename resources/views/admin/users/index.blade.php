@extends('layouts.dashboard')

@section('title', 'Users')

@section('content')
@php
    $badge = ['admin' => 'bg-brand-100 text-brand-800', 'doctor' => 'bg-sky-100 text-sky-800', 'receptionist' => 'bg-amber-100 text-amber-800', 'patient' => 'bg-emerald-100 text-emerald-800'];
@endphp

<div class="mb-6 grid grid-cols-2 gap-3 xl:grid-cols-4">
    @foreach ($roles as $r)
        <a href="{{ route('admin.users.index', ['role' => $r]) }}" class="v7-kpi {{ $role === $r ? 'ring-2 ring-brand-500' : '' }}">
            <p class="text-sm font-medium capitalize text-slate-500">{{ $r }}s</p>
            <p class="stat-val mt-2 text-3xl font-bold text-slate-900">{{ $counts[$r] ?? 0 }}</p>
        </a>
    @endforeach
</div>

<form method="GET" class="mb-5 flex flex-wrap items-center gap-3">
    <input type="search" name="q" value="{{ $q }}" placeholder="Search name, email or phone" class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm sm:w-80">
    <select name="role" onchange="this.form.requestSubmit()" class="rounded-xl border border-slate-300 bg-white px-3 py-2.5 text-sm">
        <option value="">All roles</option>
        @foreach ($roles as $r)<option value="{{ $r }}" @selected($role === $r)>{{ ucfirst($r) }}</option>@endforeach
    </select>
    <button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white">Search</button>
    @if ($q !== '' || $role)<a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-brand-700 hover:underline">Clear</a>@endif
    <span class="ml-auto text-sm text-slate-500">{{ $users->total() }} account{{ $users->total() === 1 ? '' : 's' }}</span>
</form>

<div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
    <table class="rtable w-full text-left text-sm">
        <thead class="text-xs uppercase tracking-wide text-slate-500">
            <tr><th class="px-5 py-3">Account</th><th class="px-5 py-3">Phone</th><th class="px-5 py-3">Role</th><th class="px-5 py-3">Details</th><th class="px-5 py-3">Joined</th><th class="px-5 py-3"></th></tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse ($users as $u)
                <tr>
                    <td data-label="Account" class="px-5 py-4">
                        <a href="{{ route('admin.users.show', $u) }}" class="flex items-center gap-3">
                            <span class="v7-avatar">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                            <span class="min-w-0"><span class="block font-medium text-slate-900">{{ $u->name }}</span><span class="block truncate text-xs text-slate-500">{{ $u->email }}</span></span>
                        </a>
                    </td>
                    <td data-label="Phone" class="px-5 py-4">{{ $u->phone ?: '—' }}</td>
                    <td data-label="Role" class="px-5 py-4"><span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $badge[$u->role] ?? '' }}">{{ ucfirst($u->role) }}</span></td>
                    <td data-label="Details" class="px-5 py-4">
                        @if ($u->patient) {{ $u->patient->appointments_count }} visit{{ $u->patient->appointments_count === 1 ? '' : 's' }}
                        @elseif ($u->doctor) {{ $u->doctor->specialty->name ?? 'Doctor' }}
                        @else — @endif
                    </td>
                    <td data-label="Joined" class="whitespace-nowrap px-5 py-4">{{ $u->created_at?->timezone(config('clinic.timezone'))->format('M j, Y') }}</td>
                    <td data-label="" class="actions px-5 py-4 text-right"><a href="{{ route('admin.users.show', $u) }}" class="rounded-full border border-brand-300 px-3 py-1.5 text-xs font-semibold text-brand-700 hover:bg-brand-50">View account</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-slate-500">No accounts found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">{{ $users->links() }}</div>
@endsection
