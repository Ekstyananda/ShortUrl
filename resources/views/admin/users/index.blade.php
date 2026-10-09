@extends('layouts.app')

@section('title', 'Pengguna')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
    <h1 class="h4 mb-0">Pengguna</h1>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah pengguna</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
            <tr><th>Nama</th><th>Email</th><th>Peran</th><th class="text-end">Link</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->name }} @if($user->is(auth()->user()))<span class="badge text-bg-info">Anda</span>@endif</td>
                    <td>{{ $user->email }}</td>
                    <td><span class="badge text-bg-{{ $user->isAdmin() ? 'primary' : 'secondary' }}">{{ $user->isAdmin() ? 'Admin' : 'Anggota' }}</span></td>
                    <td class="text-end">{{ $user->short_links_count }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="badge text-bg-success">Aktif</span>
                        @else
                            <span class="badge text-bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="Ubah" aria-label="Ubah"><i class="bi bi-pencil"></i></a>
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
</div>

<div class="mt-3">{{ $users->links() }}</div>
@endsection
