@if(! $link->is_active)
    <span class="badge text-bg-secondary">Nonaktif</span>
@elseif($link->isExpired())
    <span class="badge text-bg-warning">Kedaluwarsa</span>
@else
    <span class="badge text-bg-success">Aktif</span>
@endif
