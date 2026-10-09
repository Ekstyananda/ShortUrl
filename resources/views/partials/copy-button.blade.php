@php($copyLabel = $copyLabel ?? null)
<button type="button" class="btn btn-ghost {{ $copyLabel ? 'btn-sm' : 'btn-icon' }} js-copy {{ $copyClass ?? '' }}" data-copy="{{ $text }}" title="Salin link" aria-label="Salin {{ $text }}">
    <i class="bi bi-clipboard"></i>@if($copyLabel)<span class="ms-1">{{ $copyLabel }}</span>@endif
</button>
