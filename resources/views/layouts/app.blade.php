<!doctype html>
<html lang="id" data-bs-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap-icons/bootstrap-icons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body class="bg-body-tertiary d-flex flex-column min-vh-100">
@auth
<nav class="navbar navbar-expand-md bg-body border-bottom">
    <div class="container">
        <a class="navbar-brand fw-semibold" href="{{ route('links.index') }}">
            <i class="bi bi-link-45deg text-primary"></i> {{ config('app.name') }}
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link @if(request()->routeIs('links.*')) active @endif" href="{{ route('links.index') }}">Link</a>
                </li>
                @if(auth()->user()->isAdmin())
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">Pengguna</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link @if(request()->routeIs('admin.audit.*')) active @endif" href="{{ route('admin.audit.index') }}">Audit</a>
                    </li>
                @endif
            </ul>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-body-secondary">
                    {{ auth()->user()->name }}
                    <span class="badge text-bg-{{ auth()->user()->isAdmin() ? 'primary' : 'secondary' }}">{{ auth()->user()->isAdmin() ? 'Admin' : 'Anggota' }}</span>
                </span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                </form>
            </div>
        </div>
    </div>
</nav>
@endauth

<main class="container py-4 flex-grow-1">
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
    @endif
    @error('user')
        <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    @yield('content')
</main>

<footer class="py-3 text-center small text-body-secondary">
    {{ parse_url(config('app.url'), PHP_URL_HOST) }} · layanan short URL internal tim
</footer>

<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="copyToast" class="toast align-items-center text-bg-dark border-0" role="status" aria-live="polite" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">Disalin ke clipboard.</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Tutup"></button>
        </div>
    </div>
</div>

<script src="{{ asset('vendor/bootstrap/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
