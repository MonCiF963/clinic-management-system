<section class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl text-center">
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Testimonials</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Loved by our patients</h2>
        </div>

        <div class="mt-14 grid gap-6 md:grid-cols-3">
            @foreach ([
                ['Booking was so easy and the doctor was kind and professional. Highly recommended!', 'Amina K.', 'Patient'],
                ['No long waiting. I was seen on time and everything was explained clearly.', 'Yacine M.', 'Patient'],
                ['The whole family goes here now. The pediatrician is wonderful with our kids.', 'Nadia B.', 'Parent'],
            ] as [$quote, $name, $role])
                <figure class="rounded-3xl bg-white p-8 shadow-sm">
                    <div class="flex gap-1 text-amber-400">
                        @for ($i = 0; $i < 5; $i++)
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 1.5l2.7 5.5 6 .9-4.4 4.2 1 6-5.3-2.8-5.3 2.8 1-6L1.3 7.9l6-.9L10 1.5Z"/></svg>
                        @endfor
                    </div>
                    <blockquote class="mt-4 leading-relaxed">â€œ{{ $quote }}â€</blockquote>
                    <figcaption class="mt-6 flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-full bg-brand-100 font-bold text-brand-700">
                            {{ substr($name, 0, 1) }}
                        </span>
                        <span>
                            <span class="block text-sm font-semibold text-slate-900">{{ $name }}</span>
                            <span class="block text-xs">{{ $role }}</span>
                        </span>
                    </figcaption>
                </figure>
            @endforeach
        </div>
    </div>
</section>