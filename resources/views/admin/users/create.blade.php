@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7">
        <h1 class="h4 mb-3">Tambah pengguna</h1>
        <div class="card shadow-sm"><div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                @include('admin.users._form')
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
</div>
@endsection
