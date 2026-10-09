@extends('layouts.app')

@section('title', 'Statistik '.$link->alias)

@php($num = fn ($n) => number_format($n, 0, ',', '.'))
@php($refMax = max(1, (int) $topReferers->max('total')))
@php($brMax = max(1, (int) $browsers->max('total')))

@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('links.index') }}">Link</a></li>
    <li class="breadcrumb-item"><a href="{{ route('links.show', $link) }}">{{ $link->alias }}</a></li>
    <li class="breadcrumb-item active" aria-current="page">Statistik</li>
</ol></nav>

<div class="page-head">
    <div class="min-w-0">
        <h1 class="text-break">{{ parse_url(config('app.url'), PHP_URL_HOST) }}/{{ $link->alias }}</h1>
        <p><i class="bi bi-arrow-return-right me-1"></i>{{ $link->destinationHost() }}</p>
    </div>
    <div class="d-flex gap-2">
        @include('partials.copy-button', ['text' => $link->shortUrl(), 'copyLabel' => 'Salin'])
        <a href="{{ route('links.show', $link) }}" class="btn btn-sm btn-ghost">Detail link</a>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total klik', $totalClicks, 'bi-cursor', 'cyan'],
        [$days.' hari terakhir', $clicksInRange, 'bi-calendar3', ''],
        ['Hari ini', $clicksToday, 'bi-lightning-charge', 'amber'],
    ] as [$label, $value, $icon, $tone])
        <div class="col-sm-4">
            <div class="card stat">
                <div class="stat-label"><span class="stat-icon {{ $tone }}"><i class="bi {{ $icon }}"></i></span> {{ $label }}</div>
                <div class="stat-value">{{ $num($value) }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span>Klik harian</span>
        <span class="small text-muted-2 fw-normal">{{ $days }} hari terakhir</span>
    </div>
    <div class="card-body">
        <div class="chart-box"><canvas id="dailyChart" aria-label="Grafik klik harian {{ $days }} hari terakhir" role="img"></canvas></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Sumber (referer)</div>
            @forelse($topReferers as $row)
                <div class="bar-row">
                    <span class="text-truncate small fw-medium">
                        @if($row->host !== '')<i class="bi bi-globe2 text-muted-2 me-1"></i>{{ $row->host }}@else<i class="bi bi-box-arrow-in-right text-muted-2 me-1"></i>Langsung / tidak diketahui @endif
                    </span>
                    <span class="small fw-semibold">{{ $num($row->total) }}</span>
                    <div class="bar-track"><div class="bar-fill" style="width: {{ round($row->total / $refMax * 100) }}%"></div></div>
                </div>
            @empty
                <div class="empty-state py-4"><p class="mb-0 small">Belum ada data.</p></div>
            @endforelse
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header">Browser</div>
            @forelse($browsers as $row)
                <div class="bar-row">
                    <span class="small fw-medium">{{ $row->family }}</span>
                    <span class="small fw-semibold">{{ $num($row->total) }}</span>
                    <div class="bar-track"><div class="bar-fill" style="width: {{ round($row->total / $brMax * 100) }}%"></div></div>
                </div>
            @empty
                <div class="empty-state py-4"><p class="mb-0 small">Belum ada data.</p></div>
            @endforelse
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">Klik terbaru</div>
    <div class="table-responsive">
        <table class="table table-sm align-middle">
            <thead><tr><th class="ps-4">Waktu</th><th>Referer</th><th class="pe-4">Browser</th></tr></thead>
            <tbody>
            @forelse($recentClicks as $click)
                <tr>
                    <td class="ps-4 text-nowrap small">{{ $click->clicked_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i:s') }}</td>
                    <td class="small">{{ $click->referer_host ?? '—' }}</td>
                    <td class="pe-4 small">{{ $click->user_agent_family ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="ps-4 text-muted-2 small">Belum ada klik. Bagikan link Anda untuk mulai mengumpulkan data.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script type="application/json" id="dailyData">@json(['labels' => $daily->keys()->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->translatedFormat('d M'))->values(), 'values' => $daily->values()])</script>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
    (function () {
        const data = JSON.parse(document.getElementById('dailyData').textContent);
        const css = (name) => getComputedStyle(document.documentElement).getPropertyValue(name).trim();
        const ctx = document.getElementById('dailyChart');
        let chart;

        function render() {
            const brandRgb = css('--brand-rgb');
            const g = ctx.getContext('2d').createLinearGradient(0, 0, 0, 280);
            g.addColorStop(0, `rgba(${brandRgb}, .35)`);
            g.addColorStop(1, `rgba(${brandRgb}, 0)`);

            if (chart) chart.destroy();
            Chart.defaults.font.family = css('--bs-body-font-family');
            Chart.defaults.color = css('--ink-muted');
            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Klik', data: data.values, fill: true, backgroundColor: g,
                        borderColor: css('--brand'), borderWidth: 2.5, tension: .35,
                        pointRadius: 0, pointHoverRadius: 5, pointBackgroundColor: css('--brand'),
                    }],
                },
                options: {
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: { legend: { display: false }, tooltip: { padding: 10, cornerRadius: 8, displayColors: false } },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: css('--line') }, border: { display: false } },
                        x: { grid: { display: false }, ticks: { maxTicksLimit: 10 }, border: { display: false } },
                    },
                },
            });
        }

        render();
        document.addEventListener('themechange', render);
    })();
</script>
@endpush
