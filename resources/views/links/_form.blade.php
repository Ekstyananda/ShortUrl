@php($shortBase = parse_url(config('app.url'), PHP_URL_HOST).'/')
<div class="mb-4">
    <label for="destination_url" class="form-label">URL tujuan <span class="text-danger">*</span></label>
    <div class="input-group has-validation">
        <span class="input-group-text"><i class="bi bi-globe2"></i></span>
        <input id="destination_url" type="url" name="destination_url" maxlength="2048"
               value="{{ old('destination_url', $link->destination_url) }}"
               class="form-control form-control-lg @error('destination_url') is-invalid @enderror"
               placeholder="https://contoh.com/halaman-yang-sangat-panjang" required autofocus>
        @error('destination_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-text">Hanya alamat yang diawali http:// atau https://.</div>
</div>

<div class="mb-4">
    <label for="alias" class="form-label">Alias</label>
    <div class="input-group has-validation">
        <span class="input-group-text">{{ $shortBase }}</span>
        <input id="alias" type="text" name="alias" maxlength="64"
               value="{{ old('alias', $link->alias) }}"
               pattern="[A-Za-z0-9][A-Za-z0-9_\-]*"
               class="form-control @error('alias') is-invalid @enderror"
               placeholder="{{ $link->exists ? '' : 'kosongkan untuk alias acak' }}">
        @error('alias')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="form-text">Huruf, angka, <code>-</code> dan <code>_</code>; otomatis menjadi huruf kecil.
        @if($link->exists)<span class="text-warning-emphasis"><i class="bi bi-exclamation-triangle"></i> Mengubah alias membuat link lama tidak berlaku.</span>@endif
    </div>
</div>

<div class="row">
    <div class="col-md-7 mb-4">
        <label for="title" class="form-label">Judul / catatan</label>
        <input id="title" type="text" name="title" maxlength="255"
               value="{{ old('title', $link->title) }}"
               class="form-control @error('title') is-invalid @enderror"
               placeholder="Mis. Modul SBD semester ganjil">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
    <div class="col-md-5 mb-4">
        <label for="expires_at" class="form-label">Kedaluwarsa <span class="text-muted-2 fw-normal">(opsional)</span></label>
        <input id="expires_at" type="datetime-local" name="expires_at"
               value="{{ old('expires_at', $link->expires_at?->timezone(config('app.timezone'))->format('Y-m-d\TH:i')) }}"
               class="form-control @error('expires_at') is-invalid @enderror">
        @error('expires_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>
