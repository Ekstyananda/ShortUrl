<?php

namespace App\Models;

use Database\Factories\ShortLinkFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShortLink extends Model
{
    /** @use HasFactory<ShortLinkFactory> */
    use HasFactory;

    protected $fillable = [
        'alias',
        'destination_url',
        'title',
        'is_active',
        'expires_at',
    ];

    protected $attributes = [
        'is_active' => true,
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function clickEvents(): HasMany
    {
        return $this->hasMany(ClickEvent::class);
    }

    /**
     * Link yang boleh dilihat pengguna: admin melihat semua, anggota hanya miliknya.
     */
    public function scopeVisibleTo(Builder $query, User $user): Builder
    {
        return $user->isAdmin() ? $query : $query->where('user_id', $user->id);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function isRedirectable(): bool
    {
        return $this->is_active && ! $this->isExpired() && $this->user?->is_active;
    }

    public function destinationHost(): string
    {
        return (string) parse_url($this->destination_url, PHP_URL_HOST);
    }

    public function shortUrl(): string
    {
        return rtrim(config('app.url'), '/').'/'.$this->alias;
    }
}
