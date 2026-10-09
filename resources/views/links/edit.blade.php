@extends('layouts.app')

@section('title', 'Ubah '.$link->alias)

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-8">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('links.index') }}">Link</a></li>
            <li class="breadcrumb-item"><a href="{{ route('links.show', $link) }}">{{ $link->alias }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Ubah</li>
        </ol></nav>
        <div class="page-head">
            <div>
                <h1>Ubah link</h1>
                <p>Perubahan URL tujuan langsung berlaku untuk semua orang yang memakai link ini.</p>
            </div>
        </div>
        <div class="card"><div class="card-body p-4">
            <form method="POST" action="{{ route('links.update', $link) }}">
                @csrf
                @method('PATCH')
                @include('links._form')
                <div class="d-flex gap-2 pt-2">
                    <button type="submit" class="btn btn-primary">Simpan perubahan</button>
                    <a href="{{ route('links.show', $link) }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
</div>
@endsection
