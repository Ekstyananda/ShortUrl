@extends('layouts.app')

@section('title', 'Link')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">{{ auth()->user()->isAdmin() ? 'Semua link tim' : 'Link saya' }}</h1>
    <a href="{{ route('links.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat short URL</a>
</div>

<form method="GET" class="mb-3" role="search">
    <div class="input-group">
        <input type="search" name="q" value="{{ $search }}" class="form-control" placeholder="Cari alias, judul, atau URL tujuan" aria-label="Cari">
        <button class="btn btn-outline-secondary" type="submit"><i class="bi bi-search"></i></button>
    </div>
</form>

<div class="card shadow-sm">
    @if($links->isEmpty())
        <div class="card-body text-center text-body-secondary py-5">
            @if($search !== '')
                Tidak ada link yang cocok dengan "{{ $search }}".
            @else
                Belum ada short URL. <a href="{{ route('links.create') }}">Buat yang pertama</a>.
            @endif
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                <tr>
                    <th>Short URL</th>
                    <th>Tujuan</th>
                    @if(auth()->user()->isAdmin())<th>Pemilik</th>@endif
                    <th class="text-end">Klik</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
                </thead>
                <tbody>
                @foreach($links as $link)
                    <tr>
                        <td>
                            <a href="{{ route('links.show', $link) }}" class="fw-semibold text-decoration-none">/{{ $link->alias }}</a>
                            @if($link->title)<div class="small text-body-secondary text-truncate cell-max">{{ $link->title }}</div>@endif
                        </td>
                        <td>
                            <span class="badge text-bg-light border">{{ $link->destinationHost() }}</span>
                            <div class="small text-body-secondary text-truncate cell-max" title="{{ $link->destination_url }}">{{ $link->destination_url }}</div>
                        </td>
                        @if(auth()->user()->isAdmin())<td class="small">{{ $link->user->name }}</td>@endif
                        <td class="text-end">{{ number_format($link->click_events_count) }}</td>
                        <td>@include('partials.link-status')</td>
                        <td class="text-end text-nowrap">
                            @include('partials.copy-button', ['text' => $link->shortUrl()])
                            <a href="{{ route('links.analytics', $link) }}" class="btn btn-sm btn-outline-secondary" title="Statistik" aria-label="Statistik"><i class="bi bi-bar-chart"></i></a>
                            <a href="{{ route('links.edit', $link) }}" class="btn btn-sm btn-outline-secondary" title="Ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<div class="mt-3">{{ $links->links() }}</div>
@endsection
