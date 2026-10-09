@extends('layouts.app')

@section('title', 'Ubah '.$link->alias)

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h4 mb-3">Ubah short URL <code>/{{ $link->alias }}</code></h1>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('links.update', $link) }}">
                    @csrf
                    @method('PATCH')
                    @include('links._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="{{ route('links.show', $link) }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
