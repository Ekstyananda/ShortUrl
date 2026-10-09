<!doctype html>
<html lang="id">
<head>
    @include('partials.head')
    <title>@yield('title', config('app.name'))</title>
    @stack('meta')
</head>
<body class="guest-body">
@yield('content')
@include('partials.scripts')
@stack('scripts')
</body>
</html>
