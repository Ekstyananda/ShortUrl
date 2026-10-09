@extends('layouts.app')

@section('title', 'Statistik '.$link->alias)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('links.index') }}">Link</a></li>
        <li class="breadcrumb-item"><a href="{{ route('links.show', $link) }}">{{ $link->alias }}</a></li>
        <li class="breadcrumb-item active" aria-current="page">Statistik</li>
    </ol>
</nav>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Statistik <code>/{{ $link->alias }}</code></h1>
    <span class="text-body-secondary small">→ {{ $link->destinationHost() }}</span>
</div>

<div class="row g-3 mb-3">
    <div class="col-sm-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-body-secondary small">Total klik</div>
            <div class="fs-3 fw-semibold">{{ number_format($totalClicks) }}</div>
        </div></div>
    </div>
    <div class="col-sm-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-body-secondary small">{{ $days }} hari terakhir</div>
            <div class="fs-3 fw-semibold">{{ number_format($clicksInRange) }}</div>
        </div></div>
    </div>
    <div class="col-sm-4">
        <div class="card shadow-sm h-100"><div class="card-body">
            <div class="text-body-secondary small">Hari ini</div>
            <div class="fs-3 fw-semibold">{{ number_format($clicksToday) }}</div>
        </div></div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-header bg-body">Klik harian ({{ $days }} hari terakhir)</div>
    <div class="card-body">
        <div class="chart-box"><canvas id="dailyChart" aria-label="Grafik klik harian" role="img"></canvas></div>
    </div>
</div>

<div class="row g-3 mb-3">
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-body">Referer teratas</div>
            <ul class="list-group list-group-flush">
                @forelse($topReferers as $row)
                    <li class="list-group-item d-flex justify-content-between">
                        <span class="text-break">{{ $row->host !== '' ? $row->host : 'Langsung / tidak diketahui' }}</span>
                        <span class="fw-semibold">{{ number_format($row->total) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-body-secondary">Belum ada data.</li>
                @endforelse
            </ul>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-body">Browser</div>
            <ul class="list-group list-group-flush">
                @forelse($browsers as $row)
                    <li class="list-group-item d-flex justify-content-between">
                        <span>{{ $row->family }}</span>
                        <span class="fw-semibold">{{ number_format($row->total) }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-body-secondary">Belum ada data.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-body">20 klik terbaru</div>
    <div class="table-responsive">
        <table class="table table-sm mb-0">
            <thead class="table-light"><tr><th>Waktu</th><th>Referer</th><th>Browser</th></tr></thead>
            <tbody>
            @forelse($recentClicks as $click)
                <tr>
                    <td class="text-nowrap">{{ $click->clicked_at->timezone(config('app.timezone'))->format('d M Y H:i:s') }}</td>
                    <td>{{ $click->referer_host ?? '—' }}</td>
                    <td>{{ $click->user_agent_family ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-body-secondary">Belum ada klik.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<script type="application/json" id="dailyData">@json(['labels' => $daily->keys()->map(fn ($d) => \Illuminate\Support\Carbon::parse($d)->format('d/m'))->values(), 'values' => $daily->values()])</script>
@endsection

@push('scripts')
<script src="{{ asset('vendor/chartjs/chart.umd.min.js') }}"></script>
<script>
    (function () {
        const data = JSON.parse(document.getElementById('dailyData').textContent);
        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{ label: 'Klik', data: data.values, backgroundColor: '#0d6efd', borderRadius: 3 }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } }, x: { grid: { display: false } } },
            },
        });
    })();
</script>
@endpush
