@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
<div class="page-head">
    <div>
        <h1>Pengguna</h1>
        <p>Kelola akun anggota tim dan hak aksesnya.</p>
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i> Tambah pengguna</a>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
            <tr><th class="ps-4">Pengguna</th><th>Peran</th><th class="text-end">Link</th><th>Status</th><th class="text-end pe-4">Aksi</th></tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td class="ps-4">
                        <div class="d-flex align-items-center gap-3">
                            <span class="avatar">{{ mb_strtoupper(mb_substr($user->name, 0, 1)) }}</span>
                            <div class="min-w-0">
                                <div class="fw-semibold">{{ $user->name }} @if($user->is(auth()->user()))<span class="pill pill-brand ms-1">Anda</span>@endif</div>
                                <div class="small text-muted-2">{{ $user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="pill {{ $user->isAdmin() ? 'pill-brand' : 'pill-muted' }}">{{ $user->isAdmin() ? 'Admin' : 'Anggota' }}</span></td>
                    <td class="text-end fw-semibold">{{ $user->short_links_count }}</td>
                    <td><span class="pill {{ $user->is_active ? 'pill-success' : 'pill-muted' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="text-end pe-4 text-nowrap">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-ghost btn-icon" title="Ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
                        @unless($user->is(auth()->user()))
                            <form method="POST" action="{{ route('admin.users.toggle', $user) }}" class="d-inline js-confirm"
                                  data-confirm="{{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }} akun {{ $user->email }}?{{ $user->is_active ? ' Link miliknya juga berhenti mengalihkan.' : '' }}">
                                @csrf
                                @method('PATCH')
                                <button class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" type="submit">
                                    {{ $user->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                </button>
                            </form>
                        @endunless
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-footer bg-transparent d-flex justify-content-end py-3">{{ $users->links() }}</div>
    @endif
</div>
@endsection
