@php
    $tz = config('clinic.timezone');
    $cur = config('clinic.currency');
    $a = \App\Support\DashboardStats::admin();

    // week-over-week change (next 7 days vs previous 7)
    $delta = $a['prev'] > 0 ? round((($a['week'] - $a['prev']) / $a['prev']) * 100) : ($a['week'] > 0 ? 100 : 0);

    $daily = $a['daily'];
    $n = max(2, count($daily));
    $max = max(1, ...array_column($daily ?: [['c' => 1]], 'c'));
    $todayKey = now($tz)->toDateString();

    // ---- line chart geometry (viewBox 0 0 640 250) ----
    $W = 640; $H = 250; $L = 34; $R = 14; $T = 18; $B = 38;
    $iw = $W - $L - $R; $ih = $H - $T - $B;
    $pts = [];
    foreach ($daily as $i => $d) {
        $pts[] = ['x' => $L + $iw * $i / ($n - 1), 'y' => $T + $ih - ($d['c'] / $max) * $ih, 'c' => $d['c'], 'd' => $d['d']];
    }
    $line = '';
    foreach ($pts as $i => $p) {
        if ($i === 0) { $line .= sprintf('M%.1f,%.1f', $p['x'], $p['y']); continue; }
        $q = $pts[$i - 1]; $mx = ($q['x'] + $p['x']) / 2;
        $line .= sprintf(' C%.1f,%.1f %.1f,%.1f %.1f,%.1f', $mx, $q['y'], $mx, $p['y'], $p['x'], $p['y']);
    }
    $last = end($pts) ?: ['x' => $L, 'y' => $T + $ih]; $first = $pts[0] ?? ['x' => $L, 'y' => $T + $ih];
    $area = $line.sprintf(' L%.1f,%.1f L%.1f,%.1f Z', $last['x'], $T + $ih, $first['x'], $T + $ih);
    $bw = $iw / ($n - 1);

    // tiny sparkline for the "today" card
    $spark = '';
    foreach ($daily as $i => $d) {
        $sx = 100 * $i / ($n - 1); $sy = 40 - ($d['c'] / $max) * 34;
        $spark .= ($i ? ' L' : 'M').sprintf('%.1f,%.1f', $sx, $sy);
    }

    // ---- donut ----
    $colors = ['pending' => '#f5a524', 'confirmed' => '#2dd4bf', 'checked_in' => '#4f8bff', 'completed' => '#2ecc88', 'cancelled' => '#8fa9a4', 'no_show' => '#ff6b70'];
    $statusTotal = array_sum(array_column($a['by_status'], 'c'));
    $C = 2 * M_PI * 52; $cum = 0;

    $badge = ['pending' => 'bg-amber-100 text-amber-800', 'confirmed' => 'bg-brand-100 text-brand-800', 'checked_in' => 'bg-sky-100 text-sky-800', 'completed' => 'bg-emerald-100 text-emerald-800', 'cancelled' => 'bg-slate-200 text-slate-600', 'no_show' => 'bg-rose-100 text-rose-700'];

    $kpis = [
        ['Total users', number_format($a['users']), 'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m10-7.13a3 3 0 1 1-6 0 3 3 0 0 1 6 0zM7 11a3 3 0 1 0 0-6', 'All accounts', '#2dd4bf', null],
        ['Patients', number_format($a['patients']), 'M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM4 21a8 8 0 0 1 16 0', 'Registered', '#4f8bff', null],
        ['Appointments today', number_format($a['today']), 'M8 2v4M16 2v4M3 8h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z', 'vs previous week', '#2ecc88', $delta],
        ['Pending requests', number_format($a['pending']), 'M12 6v6l4 2M12 22a10 10 0 1 0 0-20 10 10 0 0 0 0 20z', 'Need a decision', '#f5a524', null],
        ['Doctors', number_format($a['doctors']), 'M12 4v16M4 12h16', 'On the team', '#7dd3fc', null],
        ['Revenue this month', number_format($a['revenue'], 0).' '.$cur, 'M3 17l6-6 4 4 8-8M14 7h7v7', 'Completed visits', '#ff9a9e', null],
    ];
@endphp

{{-- Greeting --}}
<div class="v7-greet in">
    <div>
        <p class="v7-sub">{{ now($tz)->format('l, F j, Y') }}</p>
        <h2>Welcome back, {{ auth()->user()->name }}</h2>
    </div>
    <div class="v7-actions">
        <span class="ver">Admin Dashboard V7</span>
        <a href="{{ route('appointments.index', ['status' => 'pending']) }}" class="bg-brand-600 inline-flex px-5 py-2.5 text-sm font-semibold">Review pending</a>
        <a href="{{ route('admin.users.index') }}" class="v7-soft inline-flex px-5 py-2.5 text-sm font-semibold">Manage users</a>
    </div>
</div>

{{-- KPIs --}}
<div class="v7-grid">
    @foreach ($kpis as [$label, $value, $icon, $sub, $tint, $d])
        <div class="v7-card v7-stat in" style="--tint: {{ $tint }}">
            <div class="h"><span>{{ $label }}</span><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $icon }}"/></svg></span></div>
            <p class="v stat-val">{{ $value }}</p>
            <div class="sub">
                @if ($d !== null)
                    <span class="delta {{ $d > 0 ? 'up' : ($d < 0 ? 'down' : 'flat') }}">{{ $d > 0 ? '▲' : ($d < 0 ? '▼' : '•') }} {{ abs($d) }}%</span>
                @endif
                <span>{{ $sub }}</span>
            </div>
            @if ($label === 'Appointments today')
                <svg class="sk" viewBox="0 0 100 44" preserveAspectRatio="none"><path d="{{ $spark }}" fill="none" stroke="{{ $tint }}" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" vector-effect="non-scaling-stroke" pathLength="1"/></svg>
            @endif
        </div>
    @endforeach
</div>

{{-- Chart + donut --}}
<div class="v7-row2">
    <div class="v7-card in">
        <div class="ch">
            <div><h3>Appointments</h3><small>Last 7 days and next 7 days</small></div>
            <span class="v7-chip">{{ $a['week'] }} upcoming this week</span>
        </div>
        <div class="chartbox">
            <svg viewBox="0 0 {{ $W }} {{ $H }}" class="v7-chart" role="img" aria-label="Appointments per day">
                <defs>
                    <linearGradient id="lnG" x1="0" y1="0" x2="1" y2="0"><stop offset="0" stop-color="#14b8a6"/><stop offset="1" stop-color="#ccfbf1"/></linearGradient>
                    <linearGradient id="arG" x1="0" y1="0" x2="0" y2="1"><stop offset="0" stop-color="#2dd4bf" stop-opacity=".38"/><stop offset="1" stop-color="#2dd4bf" stop-opacity="0"/></linearGradient>
                </defs>
                @foreach ([0, 1, 2, 3] as $g)
                    @php $gy = $T + $ih - ($ih / 3) * $g; @endphp
                    <line class="gl" x1="{{ $L }}" x2="{{ $W - $R }}" y1="{{ $gy }}" y2="{{ $gy }}"/>
                    <text x="{{ $L - 8 }}" y="{{ $gy + 4 }}" text-anchor="end">{{ round($max / 3 * $g) }}</text>
                @endforeach
                <path class="ar" d="{{ $area }}" fill="url(#arG)"/>
                <path class="ln" d="{{ $line }}" pathLength="1"/>
                @foreach ($pts as $p)
                    @php $isToday = $p['d'] === $todayKey; @endphp
                    <text x="{{ $p['x'] }}" y="{{ $H - 14 }}" text-anchor="middle" @if ($isToday) style="fill:#5eead4;font-weight:700" @endif>{{ \Carbon\Carbon::parse($p['d'])->format('j') }}</text>
                    <g>
                        <rect class="hit" x="{{ $p['x'] - $bw / 2 }}" y="{{ $T }}" width="{{ $bw }}" height="{{ $ih }}" fill="transparent" data-tip="<b>{{ $p['c'] }}</b> appointment(s) · {{ \Carbon\Carbon::parse($p['d'])->format('D, M j') }}"/>
                        <line class="guide" x1="{{ $p['x'] }}" x2="{{ $p['x'] }}" y1="{{ $T }}" y2="{{ $T + $ih }}"/>
                        <circle class="dotp" cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="5.5"/>
                    </g>
                    @if ($isToday)<circle class="today" cx="{{ $p['x'] }}" cy="{{ $p['y'] }}" r="4"/>@endif
                @endforeach
            </svg>
            <div class="tip"></div>
        </div>
    </div>

    <div class="v7-card in">
        <div class="ch"><div><h3>By status</h3><small>All appointments</small></div></div>
        <div class="donutwrap">
            <svg viewBox="0 0 140 140" width="168" height="168" aria-hidden="true">
                <circle cx="70" cy="70" r="52" fill="none" stroke="rgba(255,255,255,.08)" stroke-width="16"/>
                @foreach ($a['by_status'] as $row)
                    @php $len = max(0, $row['c'] / max(1, $statusTotal) * $C - 3); @endphp
                    <circle class="seg2" cx="70" cy="70" r="52" fill="none" stroke="{{ $colors[$row['status']] ?? '#8fa9a4' }}" stroke-width="16" stroke-linecap="round"
                            stroke-dasharray="{{ round($len, 2) }} {{ round($C, 2) }}" stroke-dashoffset="{{ -round($cum / max(1, $statusTotal) * $C, 2) }}" transform="rotate(-90 70 70)" style="--c: {{ round($C, 2) }}"/>
                    @php $cum += $row['c']; @endphp
                @endforeach
            </svg>
            <div class="mid"><b class="stat-val">{{ $statusTotal }}</b><span>total</span></div>
        </div>
        <div class="leg">
            @forelse ($a['by_status'] as $row)
                <div><span><i style="background: {{ $colors[$row['status']] ?? '#8fa9a4' }}; color: {{ $colors[$row['status']] ?? '#8fa9a4' }}"></i>{{ ucfirst(str_replace('_', ' ', $row['status'])) }}</span><b>{{ $row['c'] }}</b></div>
            @empty
                <div><span>No data yet.</span></div>
            @endforelse
        </div>
    </div>
</div>

{{-- Rankings + recent --}}
<div class="v7-row3">
    @foreach ([['Top doctors', $a['top_doctors']], ['Top services', $a['top_services']]] as [$title, $rows])
        <div class="v7-card in">
            <div class="ch"><h3>{{ $title }}</h3></div>
            @php $top = max(1, ...array_column($rows ?: [['c' => 1]], 'c')); @endphp
            <ul class="v7-rank">
                @forelse ($rows as $r)
                    <li>
                        <div><span>{{ $r['name'] }}</span><b>{{ $r['c'] }}</b></div>
                        <div class="v7-track"><span style="width: {{ round($r['c'] / $top * 100) }}%"></span></div>
                    </li>
                @empty
                    <li><div><span>No data yet.</span></div></li>
                @endforelse
            </ul>
        </div>
    @endforeach

    <div class="v7-card in">
        <div class="ch"><h3>Latest bookings</h3><a href="{{ route('appointments.index') }}" class="v7-link">View all</a></div>
        <div class="recent">
            @forelse ($a['recent'] as $r)
                <div class="ri">
                    <span class="av">{{ strtoupper(mb_substr($r['patient'], 0, 1)) }}</span>
                    <div class="t"><b>{{ $r['patient'] }}</b><small>{{ \Carbon\Carbon::parse($r['start_at'])->timezone($tz)->format('M j · H:i') }} · {{ $r['doctor'] }}</small></div>
                    <span class="pill-s rounded-full {{ $badge[$r['status']] ?? '' }}">{{ ucfirst(str_replace('_', ' ', $r['status'])) }}</span>
                </div>
            @empty
                <p class="v7-sub">No bookings yet.</p>
            @endforelse
        </div>
    </div>
</div>
