@extends('layouts.app')

@section('title', 'Pesan masuk')

@section('content')
<div class="page-head">
    <div>
        <h1>Pesan masuk</h1>
        <p>Pesan dari form “Hubungi kami” di halaman depan.</p>
    </div>
    <div class="btn-group" role="group" aria-label="Filter pesan">
        <a href="{{ route('admin.messages.index') }}" class="btn btn-sm {{ $filter === 'all' ? 'btn-primary' : 'btn-ghost' }}">Semua</a>
        <a href="{{ route('admin.messages.index', ['filter' => 'unread']) }}" class="btn btn-sm {{ $filter === 'unread' ? 'btn-primary' : 'btn-ghost' }}">Belum dibaca</a>
    </div>
</div>

<div class="card">
    @if($messages->isEmpty())
        <div class="empty-state">
            <div class="empty-icon"><i class="bi bi-inbox"></i></div>
            <p class="mb-0">{{ $filter === 'unread' ? 'Semua pesan sudah dibaca.' : 'Belum ada pesan masuk.' }}</p>
        </div>
    @else
        <div class="list-group list-group-flush">
            @foreach($messages as $message)
                <a href="{{ route('admin.messages.show', $message) }}" class="list-group-item list-group-item-action px-4 py-3 {{ $message->isRead() ? '' : 'msg-unread' }}">
                    <div class="d-flex align-items-center gap-3">
                        <span class="avatar">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                        <div class="min-w-0 flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                @unless($message->isRead())<span class="unread-dot" aria-label="Belum dibaca"></span>@endunless
                                <span class="msg-from">{{ $message->name }}</span>
                                @if($message->organization)<span class="small text-muted-2">· {{ $message->organization }}</span>@endif
                                <span class="ms-auto small text-muted-2 text-nowrap">{{ $message->created_at->timezone(config('app.timezone'))->translatedFormat('d M Y, H:i') }}</span>
                            </div>
                            <div class="small text-muted-2 text-truncate">{{ \Illuminate\Support\Str::limit($message->message, 140) }}</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        @if($messages->hasPages())
            <div class="card-footer bg-transparent d-flex justify-content-end py-3">{{ $messages->links() }}</div>
        @endif
    @endif
</div>
@endsection
