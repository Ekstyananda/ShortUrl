@extends('layouts.app')

@section('title', $link->alias)

@section('content')
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('links.index') }}">Link</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $link->alias }}</li>
    </ol>
</nav>

<div class="card shadow-sm mb-3">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
            <div class="min-w-0">
                <div class="mb-1">@include('partials.link-status')</div>
                <h1 class="h4 mb-1 text-break">{{ $link->shortUrl() }}</h1>
                @if($link->title)<p class="text-body-secondary mb-0">{{ $link->title }}</p>@endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                @include('partials.copy-button', ['text' => $link->shortUrl(), 'label' => 'Salin'])
                <a href="{{ route('links.analytics', $link) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-bar-chart"></i> Statistik</a>
                <a href="{{ route('links.edit', $link) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Ubah</a>
            </div>
        </div>

        <hr>

        <dl class="row mb-0">
            <dt class="col-sm-3">Host tujuan</dt>
            <dd class="col-sm-9"><span class="badge text-bg-light border fs-6">{{ $link->destinationHost() }}</span></dd>

            <dt class="col-sm-3">URL tujuan</dt>
            <dd class="col-sm-9 text-break"><code>{{ $link->destination_url }}</code></dd>

            <dt class="col-sm-3">Total klik</dt>
            <dd class="col-sm-9">{{ number_format($link->click_events_count) }}</dd>

            <dt class="col-sm-3">Pemilik</dt>
            <dd class="col-sm-9">{{ $link->user->name }} <span class="text-body-secondary">({{ $link->user->email }})</span></dd>

            <dt class="col-sm-3">Kedaluwarsa</dt>
            <dd class="col-sm-9">{{ $link->expires_at ? $link->expires_at->timezone(config('app.timezone'))->format('d M Y H:i') : '—' }}</dd>

            <dt class="col-sm-3">Dibuat</dt>
            <dd class="col-sm-9">{{ $link->created_at->timezone(config('app.timezone'))->format('d M Y H:i') }}</dd>

            <dt class="col-sm-3">Diperbarui</dt>
            <dd class="col-sm-9">{{ $link->updated_at->timezone(config('app.timezone'))->format('d M Y H:i') }}</dd>
        </dl>
    </div>
</div>

<div class="d-flex flex-wrap gap-2">
    <form method="POST" action="{{ route('links.toggle', $link) }}">
        @csrf
        @method('PATCH')
        @if($link->is_active)
            <button class="btn btn-outline-warning" type="submit"><i class="bi bi-pause-circle"></i> Nonaktifkan</button>
        @else
            <button class="btn btn-outline-success" type="submit"><i class="bi bi-play-circle"></i> Aktifkan</button>
        @endif
    </form>
    <form method="POST" action="{{ route('links.destroy', $link) }}" class="js-confirm"
          data-confirm="Hapus short URL /{{ $link->alias }} beserta seluruh statistiknya? Tindakan ini tidak bisa dibatalkan.">
        @csrf
        @method('DELETE')
        <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash"></i> Hapus</button>
    </form>
</div>
@endsection
