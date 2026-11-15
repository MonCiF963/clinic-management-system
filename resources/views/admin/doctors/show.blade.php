@extends('layouts.dashboard')

@section('title', $doctor->user->name)

@section('content')
<div class="max-w-3xl space-y-6">
    <a href="{{ route('admin.doctors.index') }}" class="text-sm font-medium text-brand-700 hover:underline">← All doctors</a>

    <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        <h2 class="text-xl font-bold text-slate-900">{{ $doctor->user->name }}</h2>
        <p class="text-sm text-slate-500">{{ $doctor->specialty->name }} · {{ $doctor->user->email }}</p>
    </div>

    <form method="POST" action="{{ route('admin.doctors.hours.add', $doctor) }}" class="grid items-end gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:grid-cols-4">
        @csrf
        <h3 class="text-lg font-semibold text-slate-900 sm:col-span-4">Add working hours ({{ config('clinic.timezone') }} time)</h3>
        <div>
            <label class="block text-sm font-medium text-slate-700">Day</label>
            <select name="weekday" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
                @foreach ($days as $i => $name)<option value="{{ $i }}" @selected(old('weekday') !== null && (int) old('weekday') === $i)>{{ $name }}</option>@endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">From</label>
            <input type="time" name="start_time" required value="{{ old('start_time', '09:00') }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700">To</label>
            <input type="time" name="end_time" required value="{{ old('end_time', '17:00') }}" class="mt-1.5 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
        </div>
        <button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">Add</button>
    </form>

    <div class="rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        @foreach ($days as $i => $name)
            <div class="flex items-start justify-between gap-4 border-b border-slate-100 px-6 py-4 last:border-0">
                <span class="w-28 font-medium text-slate-900">{{ $name }}</span>
                <div class="flex flex-1 flex-wrap gap-2">
                    @forelse ($hours[$i] ?? [] as $h)
                        <form method="POST" action="{{ route('admin.doctors.hours.remove', [$doctor, $h]) }}" class="inline-flex items-center gap-2 rounded-full bg-brand-50 px-3 py-1 text-sm text-brand-800">
                            @csrf @method('DELETE')
                            {{ substr($h->start_time, 0, 5) }} – {{ substr($h->end_time, 0, 5) }}
                            <button title="Remove" class="font-bold text-brand-700 hover:text-red-600">×</button>
                        </form>
                    @empty
                        <span class="text-sm text-slate-400">Day off</span>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
