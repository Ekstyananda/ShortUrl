@extends('layouts.app')

@section('title', 'Audit')

@php($icons = ['auth' => 'bi-box-arrow-in-right', 'link' => 'bi-link-45deg', 'user' => 'bi-person'])

@section('content')
<div class="page-head">
    <div>
        <h1>Audit aktivitas</h1>
        <p>Jejak tindakan penting seluruh anggota tim.</p>
    </div>
    <form method="GET" class="d-flex gap-2">
        <select name="action" class="form-select form-select-sm" aria-label="Filter aksi" onchange="this.form.submit()">
            <option value="">Semua aksi</option>
            @foreach($actions as $a)
                <option value="{{ $a }}" @selected($a === $action)>{{ $a }}</option>
            @endforeach
        </select>
        <noscript><button class="btn btn-sm btn-ghost" type="submit">Filter</button></noscript>
    </form>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th class="ps-4">Waktu</th><th>Pelaku</th><th>Aksi</th><th>Subjek</th><th class="pe-4">Detail</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="ps-4 text-nowrap small text-muted-2">{{ $log->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i:s') }}</td>
                    <td class="small">
                        @if($log->actor)
                            <span class="d-inline-flex align-items-center gap-2"><span class="avatar avatar-sm">{{ mb_strtoupper(mb_substr($log->actor->name, 0, 1)) }}</span>{{ $log->actor->email }}</span>
                        @else
                            <span class="text-muted-2">sistem / terhapus</span>
                        @endif
                    </td>
                    <td><span class="pill pill-brand"><i class="bi {{ $icons[strtok($log->action, '.')] ?? 'bi-dot' }}"></i> {{ $log->action }}</span></td>
                    <td class="small text-nowrap">{{ $log->subject_type ? $log->subject_type.' #'.$log->subject_id : '—' }}</td>
                    <td class="pe-4 small text-break">
                        @if($log->metadata)
                            @foreach($log->metadata as $key => $value)
                                <span class="text-muted-2">{{ $key }}:</span> {{ is_array($value) ? implode(', ', $value) : $value }}@if(! $loop->last)<br>@endif
                            @endforeach
                        @else — @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5"><div class="empty-state py-4"><p class="mb-0">Belum ada aktivitas.</p></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end py-3">{{ $logs->links() }}</div>
    @endif
</div>
@endsection
