@extends('layouts.guest')

@section('title', config('app.name').' — Short link bermerek untuk tim Anda')

@push('meta')
    <meta name="description" content="Buat short link bermerek, pantau klik secara real-time, dan kelola link tim dalam satu dashboard yang aman.">
    <meta property="og:title" content="{{ config('app.name') }}">
    <meta property="og:description" content="Short link bermerek dengan statistik klik dan kontrol tim.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url('/') }}">
@endpush

@php($host = parse_url(config('app.url'), PHP_URL_HOST))
@php($contact = config('shortlink.contact_url'))

@section('content')
<nav class="site-nav js-site-nav">
    <div class="container d-flex align-items-center py-3 gap-3">
        @include('partials.brand')
        <div class="d-none d-md-flex gap-4 ms-4 small fw-medium">
            <a href="#fitur" class="text-decoration-none text-muted-2">Fitur</a>
            <a href="#cara-kerja" class="text-decoration-none text-muted-2">Cara kerja</a>
            <a href="#keamanan" class="text-decoration-none text-muted-2">Keamanan</a>
            <a href="#kontak" class="text-decoration-none text-muted-2">Kontak</a>
        </div>
        <div class="ms-auto d-flex align-items-center gap-2">
            @include('partials.theme-toggle')
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3">Masuk</a>
        </div>
    </div>
</nav>

<header class="hero text-center">
    <div class="hero-grid"></div>
    <div class="container">
        <span class="eyebrow mb-4"><span class="dot">Baru</span> Statistik klik harian & audit tim</span>
        <h1 class="mb-4">Link pendek yang <span class="text-gradient">rapi, terukur,</span><br class="d-none d-md-inline"> dan aman untuk tim Anda</h1>
        <p class="lead mx-auto mb-5">Ubah URL panjang menjadi <strong>{{ $host }}/nama-anda</strong> yang mudah diingat, pantau setiap klik, dan kelola semua link tim dari satu dashboard.</p>

        <div class="demo-card" aria-hidden="true">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-link-45deg fs-4 text-muted-2 ms-2"></i>
                <input type="text" class="form-control" value="https://drive.google.com/file/d/1aB2cD3eF4gH5iJ6kL7mN8oP9/view?usp=sharing" readonly tabindex="-1">
                <span class="btn btn-primary text-nowrap px-4">Pendekkan</span>
            </div>
            <div class="demo-result">
                <span class="favicon"><i class="bi bi-check2"></i></span>
                <span class="short text-truncate">{{ $host }}/modul-sbd</span>
                <span class="ms-auto d-none d-sm-inline small text-muted-2"><i class="bi bi-bar-chart me-1"></i>1.284 klik</span>
                <span class="btn btn-ghost btn-sm"><i class="bi bi-clipboard"></i></span>
            </div>
        </div>

        <div class="d-flex flex-wrap justify-content-center gap-2 mt-5">
            <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-4">Masuk ke dashboard <i class="bi bi-arrow-right ms-1"></i></a>
            <a href="#kontak" class="btn btn-ghost btn-lg px-4">Hubungi kami</a>
        </div>
        <p class="logo-strip mt-4 mb-0"><i class="bi bi-shield-lock me-1"></i> Tanpa pendaftaran publik · Tanpa iklan · Data di server sendiri</p>
    </div>
</header>

<section id="fitur" class="section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Semua yang dibutuhkan untuk berbagi link</h2>
            <p class="section-sub">Dirancang untuk tim yang membagikan materi, formulir, dan tautan kampanye setiap hari.</p>
        </div>
        <div class="row g-4">
            @foreach([
                ['bi-type', 'Alias khusus', 'Pilih nama yang mudah diingat seperti /modul-sbd, atau biarkan sistem membuat alias acak.'],
                ['bi-graph-up-arrow', 'Statistik klik', 'Grafik klik harian, referer teratas, dan jenis browser untuk setiap link.'],
                ['bi-people', 'Kolaborasi tim', 'Admin mengelola akun dan semua link; anggota hanya mengelola link miliknya.'],
                ['bi-pencil-square', 'Ubah tujuan kapan saja', 'Ganti URL tujuan tanpa mengganti short link yang sudah tersebar.'],
                ['bi-clock-history', 'Kedaluwarsa & nonaktif', 'Atur tanggal kedaluwarsa atau matikan link seketika bila diperlukan.'],
                ['bi-qr-code', 'QR code otomatis', 'Setiap link langsung punya QR code siap cetak dalam format SVG atau PNG.'],
            ] as [$icon, $title, $text])
                <div class="col-sm-6 col-lg-4">
                    <div class="feature">
                        <span class="feature-icon"><i class="bi {{ $icon }}"></i></span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section id="cara-kerja" class="section pt-0">
    <div class="container">
        <div class="row align-items-center g-5">
            <div class="col-lg-5">
                <h2 class="section-title mb-4">Dari URL panjang ke link siap bagi dalam hitungan detik</h2>
                <div class="d-flex flex-column gap-4">
                    @foreach([
                        ['Tempel URL', 'Masukkan URL tujuan dari Google Drive, Form, Zoom, atau situs apa pun.'],
                        ['Beri nama', 'Tentukan alias yang mudah diucapkan dan diingat audiens Anda.'],
                        ['Bagikan & pantau', 'Salin link, bagikan, lalu lihat siapa saja yang mengklik dari dashboard.'],
                    ] as $i => [$title, $text])
                        <div class="d-flex gap-3">
                            <span class="step-num flex-shrink-0 mb-0">{{ $i + 1 }}</span>
                            <div>
                                <h3 class="h6 fw-bold mb-1">{{ $title }}</h3>
                                <p class="text-muted-2 mb-0">{{ $text }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="preview-window" aria-hidden="true">
                    <div class="chrome"><span></span><span></span><span></span></div>
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <div class="small text-muted-2">Statistik</div>
                                <div class="fw-bold">{{ $host }}/modul-sbd</div>
                            </div>
                            <span class="pill pill-success">Aktif</span>
                        </div>
                        <div class="row g-3 mb-4">
                            @foreach([['Total klik', '1.284'], ['30 hari', '836'], ['Hari ini', '47']] as [$l, $v])
                                <div class="col-4"><div class="p-3 rounded-3 border"><div class="small text-muted-2">{{ $l }}</div><div class="fs-5 fw-bold">{{ $v }}</div></div></div>
                            @endforeach
                        </div>
                        <div class="mini-bars">
                            @foreach([22, 35, 28, 46, 40, 58, 52, 70, 62, 48, 66, 80, 74, 92, 85, 64, 78, 96, 88, 100] as $h)
                                <span style="height: {{ $h }}%"></span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section id="keamanan" class="section pt-0">
    <div class="container">
        <div class="row g-4 align-items-stretch">
            <div class="col-lg-4">
                <h2 class="section-title">Aman sejak awal</h2>
                <p class="text-muted-2">Short link sering disalahgunakan untuk menyamarkan tujuan berbahaya. Karena itu keamanan bukan fitur tambahan di sini.</p>
            </div>
            @foreach([
                ['bi-shield-check', 'Validasi tujuan', 'Hanya http/https. Skema berbahaya seperti javascript: dan data: ditolak.'],
                ['bi-eye-slash', 'Ramah privasi', 'Tanpa menyimpan alamat IP. Referer disimpan sebatas nama domain.'],
                ['bi-lock', 'Akses terkendali', 'Akun dibuat admin, login dibatasi, dan HTTPS di seluruh halaman.'],
            ] as [$icon, $title, $text])
                <div class="col-sm-6 col-lg">
                    <div class="feature">
                        <span class="feature-icon"><i class="bi {{ $icon }}"></i></span>
                        <h3>{{ $title }}</h3>
                        <p>{{ $text }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<section class="section pt-0">
    <div class="container">
        <div class="cta-band text-center">
            <h2 class="fw-800 mb-3">Siap merapikan link tim Anda?</h2>
            <p class="mb-4 opacity-75 mx-auto" style="max-width: 34rem">Masuk dengan akun tim untuk mulai membuat short link. Belum punya akun? Minta admin tim Anda untuk membuatkannya.</p>
            <div class="d-flex flex-wrap justify-content-center gap-2">
                <a href="{{ route('login') }}" class="btn btn-light btn-lg px-4">Masuk sekarang</a>
                <a href="#kontak" class="btn btn-outline-light btn-lg px-4">Hubungi kami</a>
            </div>
        </div>
    </div>
</section>

<section id="kontak" class="section pt-0">
    <div class="container">
        <div class="contact-card">
            <div class="row g-0">
                <div class="col-lg-5">
                    <div class="contact-aside d-flex flex-column gap-4">
                        <div>
                            <h2 class="fw-800 mb-2">Hubungi kami</h2>
                            <p class="opacity-75 mb-0">Ingin memakai short link bermerek untuk sekolah, kampus, komunitas, atau usaha Anda? Ceritakan kebutuhan Anda, kami akan menghubungi kembali.</p>
                        </div>
                        <div class="d-flex flex-column gap-3">
                            <div class="item"><i class="bi bi-globe2"></i><div><div class="fw-semibold">Domain sendiri</div><div class="small opacity-75">Link seperti link.namaanda.id</div></div></div>
                            <div class="item"><i class="bi bi-qr-code"></i><div><div class="fw-semibold">QR code siap cetak</div><div class="small opacity-75">Untuk poster, banner, dan undangan</div></div></div>
                            <div class="item"><i class="bi bi-people"></i><div><div class="fw-semibold">Akun untuk tim</div><div class="small opacity-75">Admin & anggota dengan statistik masing-masing</div></div></div>
                        </div>
                        @if($contact)
                            <a href="{{ $contact }}" class="btn btn-light mt-auto align-self-start" rel="noopener" target="_blank"><i class="bi bi-chat-dots me-1"></i> Chat langsung</a>
                        @endif
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="p-4 p-lg-5">
                        @if(session('contact_status'))
                            <div class="alert alert-success d-flex gap-2 align-items-start" role="status">
                                <i class="bi bi-check-circle-fill mt-1"></i><span>{{ session('contact_status') }}</span>
                            </div>
                        @endif
                        @php($ce = $errors->getBag('contact'))
                        <form method="POST" action="{{ route('contact.store') }}" novalidate>
                            @csrf
                            <div class="hp-field" aria-hidden="true">
                                <label for="website">Jangan isi field ini</label>
                                <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="c-name" class="form-label">Nama <span class="text-danger">*</span></label>
                                    <input id="c-name" name="name" type="text" maxlength="100" value="{{ old('name') }}" class="form-control @if($ce->has('name')) is-invalid @endif" required autocomplete="name">
                                    @if($ce->has('name'))<div class="invalid-feedback">{{ $ce->first('name') }}</div>@endif
                                </div>
                                <div class="col-md-6">
                                    <label for="c-email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input id="c-email" name="email" type="email" maxlength="255" value="{{ old('email') }}" class="form-control @if($ce->has('email')) is-invalid @endif" required autocomplete="email">
                                    @if($ce->has('email'))<div class="invalid-feedback">{{ $ce->first('email') }}</div>@endif
                                </div>
                                <div class="col-md-6">
                                    <label for="c-phone" class="form-label">No. WhatsApp</label>
                                    <input id="c-phone" name="phone" type="tel" maxlength="30" value="{{ old('phone') }}" class="form-control @if($ce->has('phone')) is-invalid @endif" placeholder="08xxxxxxxxxx" autocomplete="tel">
                                    @if($ce->has('phone'))<div class="invalid-feedback">{{ $ce->first('phone') }}</div>@endif
                                </div>
                                <div class="col-md-6">
                                    <label for="c-org" class="form-label">Instansi / usaha</label>
                                    <input id="c-org" name="organization" type="text" maxlength="150" value="{{ old('organization') }}" class="form-control @if($ce->has('organization')) is-invalid @endif" autocomplete="organization">
                                    @if($ce->has('organization'))<div class="invalid-feedback">{{ $ce->first('organization') }}</div>@endif
                                </div>
                                <div class="col-12">
                                    <label for="c-message" class="form-label">Pesan <span class="text-danger">*</span></label>
                                    <textarea id="c-message" name="message" rows="5" maxlength="2000" class="form-control @if($ce->has('message')) is-invalid @endif" placeholder="Ceritakan kebutuhan Anda…" required>{{ old('message') }}</textarea>
                                    @if($ce->has('message'))<div class="invalid-feedback">{{ $ce->first('message') }}</div>@endif
                                </div>
                                <div class="col-12 d-flex flex-wrap align-items-center gap-3">
                                    <button type="submit" class="btn btn-primary btn-lg px-4"><i class="bi bi-send me-1"></i> Kirim pesan</button>
                                    <span class="small text-muted-2">Data Anda hanya dipakai untuk membalas pesan ini.</span>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="site-footer py-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2">
        <span>© {{ now()->year }} {{ config('app.name') }}</span>
        <span>{{ $host }}</span>
    </div>
</footer>
@endsection
