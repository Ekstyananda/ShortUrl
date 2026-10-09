@extends('layouts.app')

@section('title', 'Masuk')

@section('content')
<div class="row justify-content-center mt-md-5">
    <div class="col-sm-10 col-md-6 col-lg-4">
        <div class="text-center mb-4">
            <i class="bi bi-link-45deg display-4 text-primary"></i>
            <h1 class="h4 mt-2">{{ config('app.name') }}</h1>
            <p class="text-body-secondary small">Masuk dengan akun tim Anda.</p>
        </div>
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('login.store') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                               class="form-control @error('email') is-invalid @enderror"
                               required autofocus autocomplete="username">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               required autocomplete="current-password">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3 form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Masuk</button>
                </form>
            </div>
        </div>
        <p class="text-center small text-body-secondary mt-3">Belum punya akun? Hubungi admin tim.</p>
    </div>
</div>
@endsection
