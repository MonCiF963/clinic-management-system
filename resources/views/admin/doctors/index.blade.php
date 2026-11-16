@extends('layouts.dashboard')

@section('title', 'Doctors')

@section('content')
<div class="space-y-8">
    @if ($specialties->isEmpty())
        <p class="rounded-xl bg-amber-50 px-4 py-3 text-sm text-amber-800">Add at least one <a href="{{ route('admin.specialties.index') }}" class="font-semibold underline">specialty</a> before adding doctors.</p>
    @else
        <form method="POST" action="{{ route('admin.doctors.store') }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:grid-cols-2">
            @csrf
            <h2 class="text-lg font-semibold text-slate-900 sm:col-span-2">Add a doctor</h2>
            <input name="name" placeholder="Full name" required value="{{ old('name') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            <select name="specialty_id" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                <option value="">Specialty…</option>
                @foreach ($specialties as $sp)<option value="{{ $sp->id }}" @selected(old('specialty_id') == $sp->id)>{{ $sp->name }}</option>@endforeach
            </select>
            <input name="email" type="email" placeholder="Login email" required value="{{ old('email') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            <input name="password" type="password" placeholder="Temporary password (8+ chars, letters and numbers)" required autocomplete="new-password" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            <input name="phone" placeholder="Phone (optional)" value="{{ old('phone') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            <input name="languages" placeholder="Languages (e.g. Arabic, French)" value="{{ old('languages') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
            <textarea name="bio" rows="2" placeholder="Short bio (optional)" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-2">{{ old('bio') }}</textarea>
            <div class="sm:col-span-2"><button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">Create doctor account</button></div>
        </form>
    @endif

    <div class="overflow-x-auto rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        <table class="rtable w-full text-left text-sm">
            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                <tr><th class="px-5 py-3">Doctor</th><th class="px-5 py-3">Specialty</th><th class="px-5 py-3">Email</th><th class="px-5 py-3">Hours</th><th class="px-5 py-3"></th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse ($doctors as $d)
                    <tr>
                        <td data-label="Doctor" class="px-5 py-4 font-medium text-slate-900">{{ $d->user->name }}</td>
                        <td data-label="Specialty" class="px-5 py-4">{{ $d->specialty->name }}</td>
                        <td data-label="Email" class="px-5 py-4">{{ $d->user->email }}</td>
                        <td data-label="Hours" class="px-5 py-4">{{ $d->availabilities_count ? $d->availabilities_count.' slot(s)' : 'Not set' }}</td>
                        <td data-label="" class="actions px-5 py-4 text-right"><a href="{{ route('admin.doctors.show', $d) }}" class="font-semibold text-brand-700 hover:underline">Working hours</a></td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="px-5 py-10 text-center text-slate-500">No doctors yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
