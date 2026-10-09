@extends('layouts.app')

@section('title', 'Buat Short URL')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1 class="h4 mb-3">Buat short URL</h1>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('links.store') }}">
                    @csrf
                    @include('links._form')
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary"><i class="bi bi-scissors"></i> Buat</button>
                        <a href="{{ route('links.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
