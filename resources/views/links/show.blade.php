@extends('layouts.app')

@section('title', $link->alias)

@php($fmt = fn ($d) => $d->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i'))

@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('links.index') }}">Link</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $link->alias }}</li>
</ol></nav>

<div class="card mb-4">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="min-w-0">
                <div class="d-flex align-items-center gap-2 mb-2">
                    @include('partials.link-status')
                    @if($link->title)<span class="text-muted-2 small">{{ $link->title }}</span>@endif
                </div>
                <div class="short-url-hero">{{ $link->shortUrl() }}</div>
                <div class="text-muted-2 small mt-1 text-break"><i class="bi bi-arrow-return-right me-1"></i>{{ $link->destination_url }}</div>
            </div>
            <div class="d-flex flex-wrap gap-2">
                @include('partials.copy-button', ['text' => $link->shortUrl(), 'copyLabel' => 'Salin'])
                <a href="{{ route('links.analytics', $link) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-bar-chart me-1"></i> Statistik</a>
                <a href="{{ route('links.edit', $link) }}" class="btn btn-sm btn-ghost"><i class="bi bi-pencil me-1"></i> Ubah</a>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Detail</div>
            <div class="card-body p-4">
                <dl class="dl-grid">
                    <dt>Host tujuan</dt>
                    <dd><span class="pill pill-brand"><i class="bi bi-globe2"></i> {{ $link->destinationHost() }}</span></dd>
                    <dt>Pemilik</dt>
                    <dd class="d-flex align-items-center gap-2"><span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($link->user->name, 0, 1)) }}</span>{{ $link->user->name }} <span class="text-muted-2 small">{{ $link->user->email }}</span></dd>
                    <dt>Kedaluwarsa</dt>
                    <dd>{{ $link->expires_at ? $fmt($link->expires_at) : 'Tidak ada' }}</dd>
                    <dt>Dibuat</dt>
                    <dd>{{ $fmt($link->created_at) }}</dd>
                    <dt>Terakhir diubah</dt>
                    <dd>{{ $fmt($link->updated_at) }}</dd>
                </dl>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">QR code <span class="small text-muted-2 fw-normal">siap cetak</span></div>
            <div class="card-body text-center">
                <div class="qr-frame mb-3"><img src="{{ route('links.qr', $link) }}" alt="QR code untuk {{ $link->shortUrl() }}" width="220" height="220" id="qrImage"></div>
                <div class="d-flex justify-content-center gap-2">
                    <a href="{{ route('links.qr', ['link' => $link, 'download' => 1]) }}" class="btn btn-sm btn-ghost"><i class="bi bi-filetype-svg me-1"></i> SVG</a>
                    <button type="button" class="btn btn-sm btn-ghost js-qr-png" data-src="{{ route('links.qr', $link) }}" data-filename="qr-{{ $link->alias }}.png"><i class="bi bi-filetype-png me-1"></i> PNG</button>
                </div>
            </div>
        </div>
        <div class="card mb-4">
            <div class="card-body stat">
                <div class="stat-label"><span class="stat-icon cyan"><i class="bi bi-cursor"></i></span> Total klik</div>
                <div class="stat-value">{{ number_format($link->click_events_count, 0, ',', '.') }}</div>
                <a href="{{ route('links.analytics', $link) }}" class="small text-decoration-none">Lihat statistik lengkap <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Tindakan</div>
            <div class="card-body d-grid gap-2">
                <form method="POST" action="{{ route('links.toggle', $link) }}" class="d-grid">
                    @csrf
                    @method('PATCH')
                    @if($link->is_active)
                        <button class="btn btn-outline-warning" type="submit"><i class="bi bi-pause-circle me-1"></i> Nonaktifkan link</button>
                    @else
                        <button class="btn btn-outline-success" type="submit"><i class="bi bi-play-circle me-1"></i> Aktifkan link</button>
                    @endif
                </form>
                <form method="POST" action="{{ route('links.destroy', $link) }}" class="d-grid js-confirm"
                      data-confirm="Hapus short URL /{{ $link->alias }} beserta seluruh statistiknya? Tindakan ini tidak bisa dibatalkan.">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i> Hapus link</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
