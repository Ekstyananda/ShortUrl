@extends('layouts.app')

@section('title', 'Link')

@php($isAdmin = auth()->user()->isAdmin())
@php($shortBase = parse_url(config('app.url'), PHP_URL_HOST).'/')

@section('content')
<div class="page-head">
    <div>
        <h1>{{ $isAdmin ? 'Semua link tim' : 'Link saya' }}</h1>
        <p>Halo, {{ strtok(auth()->user()->name, ' ') }}! Ini ringkasan link {{ $isAdmin ? 'seluruh tim' : 'Anda' }}.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    @foreach([
        ['Total link', $stats['links'], 'bi-link-45deg', ''],
        ['Link aktif', $stats['active'], 'bi-lightning-charge', 'green'],
        ['Total klik', $stats['clicks'], 'bi-cursor', 'cyan'],
        ['Klik 7 hari', $stats['clicks_7d'], 'bi-graph-up-arrow', 'amber'],
    ] as [$label, $value, $icon, $tone])
        <div class="col-6 col-xl-3">
            <div class="card stat">
                <div class="stat-label"><span class="stat-icon {{ $tone }}"><i class="bi {{ $icon }}"></i></span> {{ $label }}</div>
                <div class="stat-value">{{ number_format($value, 0, ',', '.') }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="card quick-create mb-4">
    <form method="POST" action="{{ route('links.store') }}" class="row g-2 align-items-start">
        @csrf
        <div class="col-lg-6">
            <label for="qc-url" class="visually-hidden">URL tujuan</label>
            <div class="input-group">
                <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                <input id="qc-url" type="url" name="destination_url" value="{{ old('destination_url') }}" maxlength="2048"
                       class="form-control @error('destination_url') is-invalid @enderror" placeholder="Tempel URL panjang di sini…" required>
                @error('destination_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-sm-8 col-lg-4">
            <label for="qc-alias" class="visually-hidden">Alias</label>
            <div class="input-group">
                <span class="input-group-text small">{{ $shortBase }}</span>
                <input id="qc-alias" type="text" name="alias" value="{{ old('alias') }}" maxlength="64"
                       class="form-control @error('alias') is-invalid @enderror" placeholder="alias (opsional)">
                @error('alias')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="col-sm-4 col-lg-2 d-grid">
            <button type="submit" class="btn btn-primary"><i class="bi bi-scissors me-1"></i> Pendekkan</button>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>Daftar link @if($search !== '')<span class="text-muted-2 fw-normal">· hasil untuk “{{ $search }}”</span>@endif</span>
        <form method="GET" class="d-sm-none" role="search">
            <input type="search" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Cari…" aria-label="Cari link">
        </form>
        @if($search !== '')<a href="{{ route('links.index') }}" class="small text-decoration-none">Hapus filter</a>@endif
    </div>
    @if($links->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="bi {{ $search !== '' ? 'bi-search' : 'bi-link-45deg' }}"></i></div>
            @if($search !== '')
                <p class="mb-0">Tidak ada link yang cocok dengan “{{ $search }}”.</p>
            @else
                <h2 class="h6 text-body">Belum ada short link</h2>
                <p class="mb-0">Tempel URL di kolom di atas untuk membuat link pertama Anda.</p>
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                <tr>
                    <th class="ps-4">Link</th>
                    @if($isAdmin)<th class="d-none d-md-table-cell">Pemilik</th>@endif
                    <th class="text-end">Klik</th>
                    <th class="d-none d-sm-table-cell">Status</th>
                    <th class="d-none d-lg-table-cell">Dibuat</th>
                    <th class="text-end pe-4">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach($links as $link)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center gap-3">
                                <span class="favicon d-none d-sm-inline-flex">{{ mb_substr(preg_replace('/^www\./', '', $link->destinationHost()), 0, 1) }}</span>
                                <div class="min-w-0">
                                    <a href="{{ route('links.show', $link) }}" class="link-alias"><span class="host d-none d-sm-inline">{{ $shortBase }}</span><span class="host d-sm-none">/</span>{{ $link->alias }}</a>
                                    <div class="small text-muted-2 text-truncate cell-max" title="{{ $link->destination_url }}">
                                        @if($link->title)<span class="text-body">{{ $link->title }}</span> · @endif{{ $link->destination_url }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        @if($isAdmin)
                            <td class="d-none d-md-table-cell">
                                <span class="d-inline-flex align-items-center gap-2 small text-nowrap">
                                    <span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($link->user->name, 0, 1)) }}</span>{{ $link->user->name }}
                                </span>
                            </td>
                        @endif
                        <td class="text-end fw-semibold">{{ number_format($link->click_events_count, 0, ',', '.') }}</td>
                        <td class="d-none d-sm-table-cell">@include('partials.link-status')</td>
                        <td class="d-none d-lg-table-cell small text-muted-2 text-nowrap">{{ $link->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y') }}</td>
                        <td class="text-end pe-4 text-nowrap">
                            @include('partials.copy-button', ['text' => $link->shortUrl(), 'copyLabel' => null])
                            <button type="button" class="btn btn-ghost btn-icon" title="QR code" aria-label="QR code"
                                    data-bs-toggle="modal" data-bs-target="#qrModal"
                                    data-qr-src="{{ route('links.qr', $link) }}" data-qr-download="{{ route('links.qr', ['link' => $link, 'download' => 1]) }}"
                                    data-qr-url="{{ $link->shortUrl() }}" data-qr-alias="{{ $link->alias }}"><i class="bi bi-qr-code"></i></button>
                            <a href="{{ route('links.analytics', $link) }}" class="btn btn-ghost btn-icon d-none d-sm-inline-flex" title="Statistik" aria-label="Statistik"><i class="bi bi-bar-chart"></i></a>
                            <a href="{{ route('links.edit', $link) }}" class="btn btn-ghost btn-icon d-none d-sm-inline-flex" title="Ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        @if($links->hasPages())
            <div class="card-footer bg-transparent d-flex justify-content-end py-3">{{ $links->links() }}</div>
        @endif
    @endif
</div>

<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header border-0 pb-0">
                <h2 class="modal-title h6" id="qrModalTitle">QR code</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body text-center">
                <div class="qr-frame mb-2"><img src="" alt="" width="220" height="220" class="js-qr-modal-img"></div>
                <div class="small fw-semibold text-break mb-3 js-qr-modal-url"></div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="#" class="btn btn-sm btn-ghost js-qr-modal-svg"><i class="bi bi-filetype-svg me-1"></i> SVG</a>
                    <button type="button" class="btn btn-sm btn-primary js-qr-png js-qr-modal-png"><i class="bi bi-download me-1"></i> PNG</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
