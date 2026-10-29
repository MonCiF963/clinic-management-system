<footer class="bg-slate-900 text-slate-400">
    <div class="mx-auto max-w-7xl px-4 py-14 sm:px-6 lg:px-8">
        <div class="grid gap-10 sm:grid-cols-2 lg:grid-cols-4">

            <div>
                <a href="{{ url('/') }}" class="flex items-center gap-2.5">
                    <span class="flex size-9 items-center justify-center rounded-xl bg-brand-600 text-white">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" class="size-5">
                            <path d="M12 5v14M5 12h14" />
                        </svg>
                    </span>
                    <span class="text-lg font-bold text-white">{{ config('app.name') }}</span>
                </a>
                <p class="mt-4 text-sm leading-6">
                    Caring, modern and affordable healthcare for you and your family.
                </p>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Quick links</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li><a href="{{ url('/') }}#services" class="hover:text-white">Services</a></li>
                    <li><a href="{{ url('/') }}#doctors" class="hover:text-white">Our doctors</a></li>
                    <li><a href="{{ url('/') }}#reviews" class="hover:text-white">Reviews</a></li>
                    <li><a href="{{ url('/') }}#contact" class="hover:text-white">Book appointment</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Opening hours</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li>Sun – Thu: 08:00 – 18:00</li>
                    <li>Friday: Closed</li>
                    <li>Saturday: 09:00 – 14:00</li>
                    <li class="font-medium text-brand-400">Emergency: 24/7</li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold uppercase tracking-wider text-white">Contact</h3>
                <ul class="mt-4 space-y-2.5 text-sm">
                    <li>123 Health Street, Your City</li>
                    <li>+000 000 000 000</li>
                    <li>contact@yourclinic.com</li>
                </ul>
            </div>
        </div>

        <div class="mt-12 flex flex-col items-center justify-between gap-3 border-t border-slate-800 pt-6 text-sm sm:flex-row">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            <p>Built with care.</p>
        </div>
    </div>
</footer>