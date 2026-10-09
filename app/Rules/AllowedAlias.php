<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedAlias implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $reserved = array_map('strtolower', config('shortlink.reserved_aliases', []));

        if (in_array(strtolower((string) $value), $reserved, true)) {
            $fail('Alias ini dipakai oleh sistem, gunakan alias lain.');
        }
    }
}
