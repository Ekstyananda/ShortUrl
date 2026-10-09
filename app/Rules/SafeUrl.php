<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SafeUrl implements ValidationRule
{
    /**
     * Hanya terima URL absolut http/https dengan hostname, dan tolak tujuan
     * ke domain shortener sendiri agar tidak terjadi loop redirect.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value !== trim($value) || preg_match('/[\s\x00-\x1F\x7F]/', $value)) {
            $fail('URL tujuan tidak valid.');

            return;
        }

        $parts = parse_url($value);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower($parts['host'] ?? '');

        if (! in_array($scheme, ['http', 'https'], true)) {
            $fail('URL tujuan harus diawali http:// atau https://.');

            return;
        }

        if ($host === '' || filter_var($value, FILTER_VALIDATE_URL) === false) {
            $fail('URL tujuan tidak valid atau tidak memiliki hostname.');

            return;
        }

        if (isset($parts['user']) || isset($parts['pass'])) {
            $fail('URL tujuan tidak boleh berisi username atau password.');

            return;
        }

        $ownHost = strtolower((string) parse_url(config('app.url'), PHP_URL_HOST));
        if ($ownHost !== '' && $host === $ownHost) {
            $fail('URL tujuan tidak boleh mengarah ke short URL ini sendiri.');
        }
    }
}
