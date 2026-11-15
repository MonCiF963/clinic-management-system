@extends('layouts.dashboard')

@section('title', 'Specialties')

@section('content')
<div class="max-w-2xl space-y-6">
    <form method="POST" action="{{ route('admin.specialties.store') }}" class="flex gap-3 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200">
        @csrf
        <input name="name" placeholder="New specialty (e.g. Cardiology)" required maxlength="120" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
        <button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 shrink-0">Add</button>
    </form>

    <ul class="divide-y divide-slate-100 rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
        @forelse ($specialties as $sp)
            <li class="flex items-center justify-between px-6 py-4">
                <span class="font-medium text-slate-900">{{ $sp->name }} <span class="ml-2 text-xs font-normal text-slate-500">{{ $sp->doctors_count }} doctor(s)</span></span>
                @if ($sp->doctors_count === 0)
                    <form method="POST" action="{{ route('admin.specialties.destroy', $sp) }}" onsubmit="return confirm('Delete this specialty?')">
                        @csrf @method('DELETE')
                        <button class="text-sm font-semibold text-red-600 hover:underline">Delete</button>
                    </form>
                @endif
            </li>
        @empty
            <li class="px-6 py-10 text-center text-slate-500">No specialties yet.</li>
        @endforelse
    </ul>
</div>
@endsection
