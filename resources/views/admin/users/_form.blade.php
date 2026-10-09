<div class="mb-3">
    <label for="name" class="form-label">Nama</label>
    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" maxlength="255"
           class="form-control @error('name') is-invalid @enderror" required autofocus>
    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="email" class="form-label">Email</label>
    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="255"
           class="form-control @error('email') is-invalid @enderror" required autocomplete="off">
    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="mb-3">
    <label for="role" class="form-label">Peran</label>
    <select id="role" name="role" class="form-select @error('role') is-invalid @enderror">
        @php($role = old('role', $user->role ?? 'member'))
        <option value="member" @selected($role === 'member')>Anggota — kelola link sendiri</option>
        <option value="admin" @selected($role === 'admin')>Admin — kelola semua link, akun, dan audit</option>
    </select>
    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
<div class="row">
    <div class="col-md-6 mb-3">
        <label for="password" class="form-label">Password @if($user->exists)<span class="text-body-secondary small">(kosongkan jika tidak diubah)</span>@endif</label>
        <input id="password" type="password" name="password" autocomplete="new-password"
               class="form-control @error('password') is-invalid @enderror" @if(! $user->exists) required @endif>
        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="form-text">Minimal 10 karakter.</div>
    </div>
    <div class="col-md-6 mb-4">
        <label for="password_confirmation" class="form-label">Ulangi password</label>
        <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="form-control">
    </div>
</div>
