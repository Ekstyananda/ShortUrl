<!doctype html>
<html lang="id">
<head>
    @include('partials.head')
    <meta name="robots" content="noindex">
    <title>@yield('code') · {{ config('app.name') }}</title>
</head>
<body class="guest-body">
<main class="hero min-vh-100 d-flex align-items-center text-center">
    <div class="hero-grid"></div>
    <div class="container">
        <div class="mb-5">@include('partials.brand')</div>
        <div class="error-code text-gradient">@yield('code')</div>
        <h1 class="h3 mt-3">@yield('heading')</h1>
        <p class="text-muted-2 mx-auto" style="max-width: 28rem">@yield('message')</p>
        <a href="{{ url('/') }}" class="btn btn-primary mt-3"><i class="bi bi-house me-1"></i> Ke beranda</a>
    </div>
</main>
</body>
</html>
