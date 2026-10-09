@extends('layouts.app')

@section('title', 'Audit')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Audit aktivitas</h1>
    <form method="GET" class="d-flex gap-2">
        <select name="action" class="form-select form-select-sm" aria-label="Filter aksi">
            <option value="">Semua aksi</option>
            @foreach($actions as $a)
                <option value="{{ $a }}" @selected($a === $action)>{{ $a }}</option>
            @endforeach
        </select>
        <button class="btn btn-sm btn-outline-secondary" type="submit">Filter</button>
    </form>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
            <thead class="table-light"><tr><th>Waktu</th><th>Pelaku</th><th>Aksi</th><th>Subjek</th><th>Detail</th></tr></thead>
            <tbody>
            @forelse($logs as $log)
                <tr>
                    <td class="text-nowrap small">{{ $log->created_at->timezone(config('app.timezone'))->format('d M Y H:i:s') }}</td>
                    <td class="small">{{ $log->actor?->email ?? 'sistem / terhapus' }}</td>
                    <td><code>{{ $log->action }}</code></td>
                    <td class="small">{{ $log->subject_type ? $log->subject_type.' #'.$log->subject_id : '—' }}</td>
                    <td class="small text-break">
                        @if($log->metadata)
                            @foreach($log->metadata as $key => $value)
                                <span class="text-body-secondary">{{ $key }}:</span> {{ is_array($value) ? implode(', ', $value) : $value }}@if(! $loop->last)<br>@endif
                            @endforeach
                        @else — @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-body-secondary">Belum ada aktivitas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">{{ $logs->links() }}</div>
@endsection
