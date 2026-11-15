@extends('layouts.dashboard')

@section('title', 'Services')

@section('content')
<div class="space-y-8">
    <form method="POST" action="{{ route('admin.services.store') }}" class="grid gap-4 rounded-2xl bg-white p-6 shadow-sm ring-1 ring-slate-200 sm:grid-cols-6">
        @csrf
        <h2 class="text-lg font-semibold text-slate-900 sm:col-span-6">Add a service</h2>
        <input name="name" placeholder="Name (e.g. General checkup)" required maxlength="150" value="{{ old('name') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-2">
        <input name="duration_minutes" type="number" min="5" max="480" placeholder="Minutes" required value="{{ old('duration_minutes', 30) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
        <input name="price" type="number" step="0.01" min="0" placeholder="Price ({{ config('clinic.currency') }})" value="{{ old('price') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200">
        <input name="description" placeholder="Short description (optional)" maxlength="1000" value="{{ old('description') }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-2">
        <div class="sm:col-span-6"><button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">Add service</button></div>
    </form>

    <div class="space-y-3">
        @forelse ($services as $s)
            <form method="POST" action="{{ route('admin.services.update', $s) }}" class="grid items-center gap-3 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-slate-200 sm:grid-cols-12">
                @csrf @method('PUT')
                <input name="name" value="{{ $s->name }}" required maxlength="150" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-3">
                <input name="duration_minutes" type="number" min="5" max="480" value="{{ $s->duration_minutes }}" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-2" title="Minutes">
                <input name="price" type="number" step="0.01" min="0" value="{{ $s->price }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-2" title="Price">
                <input name="description" value="{{ $s->description }}" maxlength="1000" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-hidden focus:border-brand-500 focus:ring-2 focus:ring-brand-200 sm:col-span-2" placeholder="Description">
                <label class="flex items-center gap-2 text-sm sm:col-span-1">
                    <input type="checkbox" name="is_active" value="1" @checked($s->is_active) class="size-4 rounded border-slate-300 text-brand-600"> Active
                </label>
                <button class="rounded-xl bg-brand-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700 sm:col-span-2">Save</button>
            </form>
        @empty
            <p class="rounded-2xl bg-white p-8 text-center text-slate-500 ring-1 ring-slate-200">No services yet. Add your first one above.</p>
        @endforelse
    </div>
</div>
@endsection
