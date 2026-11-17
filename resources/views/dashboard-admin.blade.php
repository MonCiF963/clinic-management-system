@php
    $tz = config('clinic.timezone');
    $cur = config('clinic.currency');
    $a = \App\Support\DashboardStats::admin();

    // week-over-week change of appointments (next 7 days vs previous 7)
    $delta = $a['prev'] > 0 ? round((($a['week'] - $a['prev']) / $a['prev']) * 100) : ($a['week'] > 0 ? 100 : 0);

    $kpis = [
        ['Total users', number_format($a['users']), 'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m10-7.13a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM7 11a3 3 0 1 0 0-6', 'All accounts'],
        ['Patients', number_format($a['patients']), 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0', 'Registered'],
        ['Appointments today', number_format($a['today']), 'M8 2v4M16 2v4M3 8h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z', ($delta >= 0 ? '▲ ' : '▼ ').abs($delta).'% vs last week'],
        ['Pending requests', number_format($a['pending']), 'M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z', 'Need a decision'],
        ['Doctors', number_format($a['doctors']), 'M12 4v16M4 12h16', 'On the team'],
        ['Revenue this month', number_format($a['revenue'], 0).' '.$cur, 'M3 17l6-6 4 4 8-8M14 7h7v7', 'Completed visits'],
    ];

    // ---- bar chart geometry (viewBox 0 0 600 220) ----
    $daily = $a['daily'];
    $max = max(1, ...array_column($daily ?: [['c' => 1]], 'c'));
    $n = max(1, count($daily));
    $bw = 600 / $n;
    $todayKey = now($tz)->toDateString();

    // ---- donut ----
    $colors = ['pending' => '#f59e0b', 'confirmed' => '#2dd4bf', 'checked_in' => '#38bdf8', 'completed' => '#22c55e', 'cancelled' => '#94a3b8', 'no_show' => '#f43f5e'];
    $total = max(1, array_sum(array_column($a['by_status'], 'c')));
    $stops = []; $acc = 0;
    foreach ($a['by_status'] as $row) {
        $from = $acc / $total * 100; $acc += $row['c']; $to = $acc / $total * 100;
        $stops[] = ($colors[$row['status']] ?? '#cbd5e1')." {$from}% {$to}%";
    }
    $conic = $stops ? implode(', ', $stops) : 'rgba(255,255,255,.1) 0 100%';

    $badge = ['pending' => 'bg-amber-100 text-amber-800', 'confirmed' => 'bg-brand-100 text-brand-800', 'checked_in' => 'bg-sky-100 text-sky-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-slate-200 text-slate-600', 'no_show' => 'bg-rose-100 text-rose-700'];
@endphp

{{-- Hero --}}
<div class="gcard v7-hero in rounded-3xl p-6 sm:p-8">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <p class="text-sm font-medium text-brand-100">{{ now($tz)->format('l, F j, Y') }}</p>
            <h2 class="mt-1 text-2xl font-bold text-white sm:text-3xl">Welcome back, {{ auth()->user()->name }}</h2>
            <p class="mt-1 text-brand-100">Here is how the clinic is doing.</p>
        </div>
        <span class="ver">Admin Dashboard V7</span>
    </div>
    <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="hero-btn btn-shine inline-flex rounded-full px-5 py-2.5 text-sm font-semibold">Review pending</a>
        <a href="{{ route('admin.users.index') }}" class="v7-ghost inline-flex rounded-full px-5 py-2.5 text-sm font-semibold">Manage users</a>
        <a href="{{ route('admin.doctors.index') }}" class="v7-ghost inline-flex rounded-full px-5 py-2.5 text-sm font-semibold">Doctors</a>
    </div>
</div>

{{-- KPIs --}}
<div class="mt-6 grid grid-cols-2 gap-3 sm:gap-4 xl:grid-cols-3">
    @foreach ($kpis as [$label, $value, $icon, $sub])
        <div class="v7-kpi rounded-2xl p-4 sm:p-5">
            <div class="flex items-center justify-between">
                <p class="text-sm font-medium text-slate-500">{{ $label }}</p>
                <span class="v7-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg></span>
            </div>
            <p class="stat-val mt-3 text-3xl font-bold text-slate-900">{{ $value }}</p>
            <p class="mt-1 text-xs font-medium {{ $label === 'Appointments today' ? ($delta >= 0 ? 'text-emerald-600' : 'text-rose-600') : 'text-slate-400' }}">{{ $sub }}</p>
        </div>
    @endforeach
</div>

{{-- Charts --}}
<div class="mt-6 grid gap-4 lg:grid-cols-3">
    <div class="v7-card rounded-2xl p-5 sm:p-6 lg:col-span-2">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-semibold text-slate-900">Appointments</h3>
                <p class="text-xs text-slate-500">Last 7 days and next 7 days</p>
            </div>
            <span class="rounded-full bg-brand-100 px-3 py-1 text-xs font-semibold text-brand-700">{{ $a['week'] }} upcoming this week</span>
        </div>
        <svg viewBox="0 0 600 220" class="v7-chart mt-4 w-full" role="img" aria-label="Appointments per day">
            <defs>
                <linearGradient id="bar" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#5eead4"/><stop offset="1" stop-color="#0d9488"/></linearGradient>
                <linearGradient id="barToday" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#99f6e4"/><stop offset="1" stop-color="#0f766e"/></linearGradient>
            </defs>
            @foreach ([0, 1, 2, 3] as $g)
                <line x1="0" x2="600" y1="{{ 20 + $g * 45 }}" y2="{{ 20 + $g * 45 }}" stroke="#fff" stroke-opacity=".07"/>
            @endforeach
            @foreach ($daily as $i => $d)
                @php
                    $h = $d['c'] ? max(8, $d['c'] / $max * 150) : 4;
                    $x = $i * $bw + $bw * 0.2;
                    $isToday = $d['d'] === $todayKey;
                @endphp
                <g class="v7-bar">
                    <rect x="{{ $x }}" y="{{ 170 - $h }}" width="{{ $bw * 0.6 }}" height="{{ $h }}" rx="8" fill="{{ $d['c'] ? ($isToday ? 'url(#barToday)' : 'url(#bar)') : 'rgba(255,255,255,.08)' }}" style="--i: {{ $i }}"/>
                    @if ($d['c'])
                        <text x="{{ $x + $bw * 0.3 }}" y="{{ 163 - $h }}" text-anchor="middle" font-size="11" font-weight="600" fill="#5eead4">{{ $d['c'] }}</text>
                    @endif
                    <text x="{{ $x + $bw * 0.3 }}" y="192" text-anchor="middle" font-size="10.5" fill="{{ $isToday ? '#5eead4' : '#8fa9a4' }}" font-weight="{{ $isToday ? 700 : 500 }}">{{ \Carbon\Carbon::parse($d['d'])->format('j') }}</text>
                    <text x="{{ $x + $bw * 0.3 }}" y="206" text-anchor="middle" font-size="9" fill="#5f7a75">{{ \Carbon\Carbon::parse($d['d'])->format('D') }}</text>
                    <title>{{ \Carbon\Carbon::parse($d['d'])->format('D, M j') }}: {{ $d['c'] }} appointment(s)</title>
                </g>
            @endforeach
        </svg>
    </div>

    <div class="v7-card rounded-2xl p-5 sm:p-6">
        <h3 class="text-base font-semibold text-slate-900">By status</h3>
        <p class="text-xs text-slate-500">All appointments</p>
        <div class="mt-5 flex items-center gap-5">
            <div class="v7-donut" style="background: conic-gradient({{ $conic }})"><span><b class="stat-val">{{ array_sum(array_column($a['by_status'], 'c')) }}</b>total</span></div>
            <ul class="min-w-0 flex-1 space-y-2 text-sm">
                @forelse ($a['by_status'] as $row)
                    <li class="flex items-center justify-between gap-2">
                        <span class="flex items-center gap-2 truncate text-slate-600"><i class="size-2.5 shrink-0 rounded-full" style="background: {{ $colors[$row['status']] ?? '#cbd5e1' }}"></i>{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</span>
                        <b class="text-slate-900">{{ $row['c'] }}</b>
                    </li>
                @empty
                    <li class="text-slate-500">No data yet.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

{{-- Rankings + recent --}}
<div class="mt-4 grid gap-4 lg:grid-cols-3">
    @foreach ([['Top doctors', $a['top_doctors']], ['Top services', $a['top_services']]] as [$title, $rows])
        <div class="v7-card rounded-2xl p-5 sm:p-6">
            <h3 class="text-base font-semibold text-slate-900">{{ $title }}</h3>
            <ul class="mt-4 space-y-4">
                @php $top = max(1, ...array_column($rows ?: [['c' => 1]], 'c')); @endphp
                @forelse ($rows as $r)
                    <li>
                        <div class="flex justify-between text-sm"><span class="truncate text-slate-700">{{ $r['name'] }}</span><b class="text-slate-900">{{ $r['c'] }}</b></div>
                        <div class="v7-track mt-1.5"><span style="width: {{ round($r['c'] / $top * 100) }}%"></span></div>
                    </li>
                @empty
                    <li class="text-sm text-slate-500">No data yet.</li>
                @endforelse
            </ul>
        </div>
    @endforeach

    <div class="v7-card rounded-2xl p-5 sm:p-6">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-semibold text-slate-900">Latest bookings</h3>
            <a href="{{ route('appointments.index') }}" class="text-xs font-semibold text-brand-700 hover:underline">View all</a>
        </div>
        <ul class="mt-4 space-y-3">
            @forelse ($a['recent'] as $r)
                <li class="flex items-center justify-between gap-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-medium text-slate-800">{{ $r['patient'] }}</p>
                        <p class="truncate text-xs text-slate-500">{{ \Carbon\Carbon::parse($r['start_at'])->timezone($tz)->format('M j · H:i') }} · {{ $r['doctor'] }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $badge[$r['status']] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $r['status'])) }}</span>
                </li>
            @empty
                <li class="text-sm text-slate-500">No bookings yet.</li>
            @endforelse
        </ul>
    </div>
</div>
