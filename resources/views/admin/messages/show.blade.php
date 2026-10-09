@extends('layouts.app')

@section('title', 'Pesan dari '.$message->name)

@section('content')
<nav aria-label="breadcrumb"><ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{ route('admin.messages.index') }}">Pesan masuk</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $message->name }}</li>
</ol></nav>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <span class="avatar">{{ mb_strtoupper(mb_substr($message->name, 0, 1)) }}</span>
                    <div>
                        <div class="fw-bold">{{ $message->name }}</div>
                        <div class="small text-muted-2">{{ $message->created_at->timezone(config('app.timezone'))->translatedFormat('l, d F Y · H:i') }}</div>
                    </div>
                </div>
                <div class="message-body">{{ $message->message }}</div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card mb-4">
            <div class="card-header">Kontak</div>
            <div class="card-body">
                <dl class="mb-0">
                    <dt class="small text-muted-2 fw-medium">Email</dt>
                    <dd class="text-break">{{ $message->email }}</dd>
                    @if($message->phone)
                        <dt class="small text-muted-2 fw-medium">WhatsApp</dt>
                        <dd>{{ $message->phone }}</dd>
                    @endif
                    @if($message->organization)
                        <dt class="small text-muted-2 fw-medium">Instansi / usaha</dt>
                        <dd class="mb-0">{{ $message->organization }}</dd>
                    @endif
                </dl>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Tindakan</div>
            <div class="card-body d-grid gap-2">
                <a href="mailto:{{ rawurlencode($message->email) }}?subject={{ rawurlencode('Re: '.config('app.name')) }}" class="btn btn-primary"><i class="bi bi-envelope me-1"></i> Balas via email</a>
                @if($wa = $message->whatsappNumber())
                    <a href="https://wa.me/{{ $wa }}" target="_blank" rel="noopener" class="btn btn-outline-success"><i class="bi bi-whatsapp me-1"></i> Balas via WhatsApp</a>
                @endif
                <form method="POST" action="{{ route('admin.messages.unread', $message) }}" class="d-grid">
                    @csrf
                    @method('PATCH')
                    <button class="btn btn-ghost" type="submit"><i class="bi bi-envelope-dot me-1"></i> Tandai belum dibaca</button>
                </form>
                <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" class="d-grid js-confirm" data-confirm="Hapus pesan dari {{ $message->name }}?">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-outline-danger" type="submit"><i class="bi bi-trash me-1"></i> Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
