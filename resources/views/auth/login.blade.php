@extends('layouts.guest')

@section('title', 'Masuk · '.config('app.name'))

@section('content')
<div class="auth-wrap">
    <aside class="auth-aside">
        @include('partials.brand')
        <div>
            <blockquote class="mb-4">“Satu tempat untuk semua link tim — rapi dibagikan, jelas hasilnya.”</blockquote>
            <div class="glass-card d-flex align-items-center gap-3">
                <span class="brand-mark"><i class="bi bi-graph-up-arrow"></i></span>
                <div>
                    <div class="small opacity-75">Statistik per link</div>
                    <div class="fs-5 fw-bold">Pantau setiap klik</div>
                </div>
            </div>
        </div>
        <div class="small opacity-75">{{ parse_url(config('app.url'), PHP_URL_HOST) }}</div>
    </aside>

    <div class="auth-form">
        <div class="inner">
            <div class="d-flex justify-content-between align-items-center mb-5">
                <span class="d-lg-none">@include('partials.brand')</span>
                <a href="{{ url('/') }}" class="small text-decoration-none text-muted-2 d-none d-lg-inline"><i class="bi bi-arrow-left me-1"></i> Beranda</a>
                @include('partials.theme-toggle')
            </div>

            <h1 class="h3 mb-1">Selamat datang kembali</h1>
            <p class="text-muted-2 mb-4">Masuk dengan akun tim Anda.</p>

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="nama@krayna.id" required autofocus autocomplete="username">
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group has-validation">
                        <input id="password" type="password" name="password"
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="••••••••••" required autocomplete="current-password">
                        <button class="btn btn-ghost js-toggle-password" type="button" data-target="password" aria-label="Tampilkan password"><i class="bi bi-eye"></i></button>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-4 form-check">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember">
                    <label class="form-check-label small" for="remember">Ingat saya di perangkat ini</label>
                </div>
                <button type="submit" class="btn btn-primary btn-lg w-100">Masuk</button>
            </form>

            <p class="small text-muted-2 mt-4 mb-0"><i class="bi bi-info-circle me-1"></i> Pendaftaran tidak dibuka untuk umum. Hubungi admin tim untuk mendapatkan akun.</p>
        </div>
    </div>
</div>
@endsection
