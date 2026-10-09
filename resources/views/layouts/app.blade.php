<!doctype html>
<html lang="id">
<head>
    @include('partials.head')
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Dashboard') · {{ config('app.name') }}</title>
</head>
<body>
@php($me = auth()->user())
<div class="app-shell">
    <aside class="sidebar offcanvas-md offcanvas-start" tabindex="-1" id="sidebar" aria-label="Navigasi utama">
        <div class="offcanvas-header border-bottom">
            @include('partials.brand', ['href' => route('links.index')])
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-none d-md-block px-2 mb-3">
                @include('partials.brand', ['href' => route('links.index')])
            </div>

            <a href="{{ route('links.create') }}" class="btn btn-primary w-100 mb-2"><i class="bi bi-plus-lg me-1"></i> Buat link</a>

            <div class="sidebar-section">Menu</div>
            <nav class="d-flex flex-column gap-1">
                <a class="side-link @if(request()->routeIs('links.index', 'links.show', 'links.edit', 'links.analytics')) active @endif" href="{{ route('links.index') }}">
                    <i class="bi bi-grid-1x2"></i> {{ $me->isAdmin() ? 'Semua link' : 'Link saya' }}
                </a>
                <a class="side-link @if(request()->routeIs('links.create')) active @endif" href="{{ route('links.create') }}">
                    <i class="bi bi-plus-square"></i> Link baru
                </a>
            </nav>

            @if($me->isAdmin())
                <div class="sidebar-section">Admin</div>
                <nav class="d-flex flex-column gap-1">
                    <a class="side-link @if(request()->routeIs('admin.users.*')) active @endif" href="{{ route('admin.users.index') }}">
                        <i class="bi bi-people"></i> Pengguna
                    </a>
                    @php($unreadMessages = \App\Models\ContactMessage::unread()->count())
                    <a class="side-link @if(request()->routeIs('admin.messages.*')) active @endif" href="{{ route('admin.messages.index') }}">
                        <i class="bi bi-inbox"></i> Pesan masuk
                        @if($unreadMessages)<span class="side-badge">{{ $unreadMessages }}</span>@endif
                    </a>
                    <a class="side-link @if(request()->routeIs('admin.audit.*')) active @endif" href="{{ route('admin.audit.index') }}">
                        <i class="bi bi-shield-check"></i> Audit aktivitas
                    </a>
                </nav>
            @endif

            <div class="mt-auto pt-4">
                <div class="user-chip">
                    <span class="avatar">{{ mb_strtoupper(mb_substr($me->name, 0, 1)) }}</span>
                    <div class="min-w-0 flex-grow-1">
                        <div class="fw-semibold small text-truncate">{{ $me->name }}</div>
                        <div class="text-muted-2 small text-truncate">{{ $me->isAdmin() ? 'Admin' : 'Anggota' }}</div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-ghost btn-icon" type="submit" title="Keluar" aria-label="Keluar"><i class="bi bi-box-arrow-right"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </aside>

    <div class="app-main">
        <header class="topbar">
            <div class="topbar-inner d-flex align-items-center gap-2 py-2">
                <button class="btn btn-ghost btn-icon d-md-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="Buka menu">
                    <i class="bi bi-list"></i>
                </button>
                <span class="d-md-none">@include('partials.brand', ['href' => route('links.index')])</span>
                <form action="{{ route('links.index') }}" method="GET" class="d-none d-sm-block ms-md-0 flex-grow-1" role="search" style="max-width: 26rem">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-transparent"><i class="bi bi-search"></i></span>
                        <input type="search" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="Cari link…" aria-label="Cari link">
                    </div>
                </form>
                <div class="ms-auto d-flex align-items-center gap-2">
                    @include('partials.theme-toggle')
                </div>
            </div>
        </header>

        <main class="app-content">
            @if (session('status'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2" role="alert">
                    <i class="bi bi-check-circle-fill"></i> <span>{{ session('status') }}</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
                </div>
            @endif
            @error('user')
                <div class="alert alert-danger d-flex align-items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> {{ $message }}</div>
            @enderror

            @yield('content')
        </main>
    </div>
</div>

@include('partials.scripts')
@stack('scripts')
</body>
</html>
