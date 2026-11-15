<section id="appointment" class="py-20 lg:py-28">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="relative overflow-hidden rounded-[2.5rem] bg-linear-to-br from-brand-600 to-brand-900 px-6 py-14 sm:px-12 lg:px-16">
            <div class="absolute -top-20 -right-20 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>

            <div class="relative grid items-center gap-12 lg:grid-cols-2">
                <div class="text-white">
                    <h2 class="text-3xl font-extrabold sm:text-4xl">Book your appointment today</h2>
                    <p class="mt-4 text-brand-100">
                        Fill in the form and our team will confirm your visit shortly.
                        For emergencies, call us any time, day or night.
                    </p>
                    <p class="mt-8 text-sm text-brand-100">Emergency line</p>
                    <p class="text-2xl font-bold">+000 000 000 000</p>
                </div>

                <div class="grid gap-4 rounded-3xl bg-white p-6 text-slate-700 shadow-2xl sm:p-8">
                    <h3 class="text-xl font-bold text-slate-900">Book online in 3 steps</h3>
                    <ol class="list-decimal space-y-2 pl-5 text-sm">
                        <li>Create your free account.</li>
                        <li>Choose a service, a doctor and a day.</li>
                        <li>Pick a free time. We confirm your visit.</li>
                    </ol>
                    @auth
                        <a href="{{ auth()->user()->hasRole('patient') ? route('appointments.create') : route('dashboard') }}"
                           class="rounded-xl bg-brand-600 px-6 py-3.5 text-center font-semibold text-white transition hover:bg-brand-700">
                            {{ auth()->user()->hasRole('patient') ? 'Book an appointment' : 'Go to dashboard' }}
                        </a>
                    @else
                        <a href="{{ route('register') }}" class="rounded-xl bg-brand-600 px-6 py-3.5 text-center font-semibold text-white transition hover:bg-brand-700">Create an account</a>
                        <a href="{{ route('login') }}" class="rounded-xl border border-slate-300 px-6 py-3.5 text-center font-semibold text-slate-700 transition hover:border-brand-600 hover:text-brand-700">I already have an account</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</section>