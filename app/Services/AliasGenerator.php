<?php

namespace App\Services;

use App\Models\ShortLink;
use RuntimeException;

class AliasGenerator
{
    // Tanpa karakter yang mudah tertukar (0/o, 1/l/i).
    private const ALPHABET = 'abcdefghjkmnpqrstuvwxyz23456789';

    /**
     * Buat alias acak yang belum dipakai dan tidak termasuk alias terlarang.
     */
    public static function generate(?int $length = null): string
    {
        $length ??= config('shortlink.random_alias_length', 6);
        $reserved = array_map('strtolower', config('shortlink.reserved_aliases', []));
        $max = strlen(self::ALPHABET) - 1;

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $alias = '';
            for ($i = 0; $i < $length; $i++) {
                $alias .= self::ALPHABET[random_int(0, $max)];
            }

            if (! in_array($alias, $reserved, true)
                && ! ShortLink::where('alias', $alias)->exists()) {
                return $alias;
            }
        }

        throw new RuntimeException('Gagal membuat alias unik, coba lagi.');
    }
}
