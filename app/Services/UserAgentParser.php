<?php

namespace App\Services;

class UserAgentParser
{
    /**
     * Klasifikasi sederhana keluarga browser. Hanya nama keluarga yang disimpan,
     * bukan string User-Agent lengkap.
     */
    public static function family(?string $userAgent): ?string
    {
        if ($userAgent === null || $userAgent === '') {
            return null;
        }

        $ua = substr($userAgent, 0, 512);

        $rules = [
            'Bot' => '/bot|crawl|spider|slurp|preview|facebookexternalhit|whatsapp|telegram|curl|wget|python|httpclient|okhttp|go-http/i',
            'Edge' => '/Edg(e|A|iOS)?\//',
            'Opera' => '/OPR\/|Opera/',
            'Samsung Internet' => '/SamsungBrowser\//',
            'Firefox' => '/Firefox\/|FxiOS\//',
            'Chrome' => '/Chrome\/|CriOS\//',
            'Safari' => '/Safari\//',
        ];

        foreach ($rules as $family => $pattern) {
            if (preg_match($pattern, $ua)) {
                return $family;
            }
        }

        return 'Other';
    }
}
