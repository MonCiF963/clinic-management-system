@php
    $doctors = [
        ['Dr. Sarah Ahmed', 'Cardiologist', 'SA', 'from-brand-500 to-sky-500'],
        ['Dr. Karim Benali', 'General Practitioner', 'KB', 'from-sky-500 to-indigo-500'],
        ['Dr. Lina Haddad', 'Pediatrician', 'LH', 'from-pink-500 to-rose-500'],
        ['Dr. Omar Saidi', 'Dermatologist', 'OS', 'from-amber-500 to-orange-500'],
    ];
@endphp

<section id="doctors" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Our team</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Meet our doctors</h2>
            <p class="mt-4">Experienced, friendly professionals dedicated to your wellbeing.</p>
        </div>

        <div class="mt-14 grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($doctors as [$name, $specialty, $initials, $gradient])
                <div class="group overflow-hidden rounded-3xl border border-slate-100 bg-white text-center transition duration-300 hover:-translate-y-2 hover:shadow-xl hover:shadow-slate-200/70">
                    <div class="bg-linear-to-br {{ $gradient }} flex h-40 items-center justify-center">
                        <span class="flex h-24 w-24 items-center justify-center rounded-full bg-white/20 text-3xl font-bold text-white ring-4 ring-white/40">
                            {{ $initials }}
                        </span>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-slate-900">{{ $name }}</h3>
                        <p class="text-sm text-brand-600">{{ $specialty }}</p>
                        <a href="#appointment" class="mt-4 inline-block rounded-full bg-slate-100 px-5 py-2 text-sm font-semibold text-slate-700 transition group-hover:bg-brand-600 group-hover:text-white">
                            Book now
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>