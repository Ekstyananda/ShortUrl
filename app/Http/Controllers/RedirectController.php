<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Services\UserAgentParser;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class RedirectController extends Controller
{
    public function __invoke(Request $request, string $alias): RedirectResponse
    {
        $link = ShortLink::with('user:id,is_active')->where('alias', strtolower($alias))->first();

        abort_if(! $link || ! $link->isRedirectable(), 404);

        try {
            $link->clickEvents()->create([
                'clicked_at' => now(),
                'referer_host' => $this->refererHost($request),
                'user_agent_family' => UserAgentParser::family($request->userAgent()),
            ]);
        } catch (Throwable $e) {
            // Kegagalan pencatatan statistik tidak boleh menggagalkan redirect.
            Log::warning('Gagal mencatat klik', ['alias' => $alias, 'error' => $e->getMessage()]);
        }

        return redirect()->away($link->destination_url, 302)->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'X-Robots-Tag' => 'noindex',
        ]);
    }

    /**
     * Hanya hostname referer yang disimpan, bukan URL lengkap.
     */
    private function refererHost(Request $request): ?string
    {
        $referer = $request->headers->get('referer');
        if (! $referer) {
            return null;
        }

        $host = parse_url(substr($referer, 0, 2048), PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return null;
        }

        return substr(strtolower($host), 0, 255);
    }
}
