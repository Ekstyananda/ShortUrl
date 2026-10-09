@extends('layouts.app')

@section('title', 'Tambah pengguna')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-7">
        <nav aria-label="breadcrumb"><ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Pengguna</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah pengguna</li>
        </ol></nav>
        <div class="page-head">
            <div>
                <h1>Tambah pengguna</h1>
                <p>Akun baru langsung aktif dan dapat login.</p>
            </div>
        </div>
        <div class="card"><div class="card-body p-4">
            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf
                @include('admin.users._form')
                <div class="d-flex gap-2">
                    <button class="btn btn-primary" type="submit">Simpan</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost">Batal</a>
                </div>
            </form>
        </div></div>
    </div>
</div>
@endsection
