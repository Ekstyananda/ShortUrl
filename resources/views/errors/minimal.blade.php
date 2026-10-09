<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex">
    <title>@yield('code') · {{ config('app.name') }}</title>
    <link rel="stylesheet" href="{{ asset('vendor/bootstrap/bootstrap.min.css') }}">
</head>
<body class="bg-body-tertiary d-flex align-items-center min-vh-100">
<main class="container text-center py-5">
    <div class="display-3 fw-bold text-body-secondary">@yield('code')</div>
    <h1 class="h4 mt-2">@yield('heading')</h1>
    <p class="text-body-secondary">@yield('message')</p>
</main>
</body>
</html>
