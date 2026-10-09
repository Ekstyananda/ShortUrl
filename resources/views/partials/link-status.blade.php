@if(! $link->is_active)
    <span class="pill pill-muted">Nonaktif</span>
@elseif($link->isExpired())
    <span class="pill pill-warning">Kedaluwarsa</span>
@else
    <span class="pill pill-success">Aktif</span>
@endif
