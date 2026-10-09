@extends('layouts.app')

@section('title', 'Buat Short URL')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('links.index') }}">Link</a></li>
            <li class="breadcrumb-item active" aria-current="page">Baru</li>
        </ol></nav>
        <div class="page-head">
            <div>
                <h1>Buat short link</h1>
                <p>Tempel URL tujuan, beri alias yang mudah diingat, lalu bagikan.</p>
            </div>
        </div>
        <div class="card"><div class="card-body p-4">
            <form method="POST" action="{{ route('links.store') }}">
                @csrf
                @include('links._form')
                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-scissors me-1"></i> Buat link</button>
                    <a href="{{ route('links.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
</div>
@endsection
