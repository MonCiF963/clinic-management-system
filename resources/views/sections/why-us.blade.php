<section id="about" class="bg-slate-50 py-20 lg:py-28">
    <div class="mx-auto grid max-w-7xl items-center gap-14 px-4 sm:px-6 lg:grid-cols-2 lg:px-8">

        <div>
            <p class="text-sm font-semibold uppercase tracking-wider text-brand-600">Why choose us</p>
            <h2 class="mt-2 text-3xl font-extrabold text-slate-900 sm:text-4xl">Care built around you</h2>
            <p class="mt-4 leading-relaxed">
                We combine experienced doctors with a simple digital experience, so your visit is
                smooth from the first click to the follow-up.
            </p>

            <ul class="mt-8 space-y-5">
                @foreach ([
                    ['Experienced specialists', 'Qualified doctors across many medical fields.'],
                    ['Modern equipment', 'Up-to-date diagnostic tools for accurate results.'],
                    ['Short waiting times', 'Online scheduling means less time in the waiting room.'],
                    ['Private & secure records', 'Your medical information is protected and confidential.'],
                ] as [$title, $text])
                    <li class="flex gap-4">
                        <span class="mt-1 flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-brand-600 text-white">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7" />
                            </svg>
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $title }}</p>
                            <p class="text-sm">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="rounded-[2.5rem] bg-white p-8 shadow-xl shadow-slate-200/60 sm:p-10">
            <h3 class="text-xl font-bold text-slate-900">How it works</h3>
            <ol class="mt-8 space-y-8">
                @foreach ([
                    ['Choose your doctor', 'Browse specialists and pick the right one.'],
                    ['Pick a date & time', 'Select a slot that fits your schedule.'],
                    ['Get treated', 'Arrive, get seen on time, and receive your care plan.'],
                ] as $i => [$title, $text])
                    <li class="flex gap-5">
                        <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-linear-to-br from-brand-500 to-brand-700 text-lg font-bold text-white">
                            {{ $i + 1 }}
                        </span>
                        <div>
                            <p class="font-semibold text-slate-900">{{ $title }}</p>
                            <p class="text-sm">{{ $text }}</p>
                        </div>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
</section>