<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use App\Services\QrCodeGenerator;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;

class QrCodeController extends Controller
{
    public function show(Request $request, ShortLink $link): Response
    {
        Gate::authorize('view', $link);

        $headers = [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'private, max-age=300',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Security-Policy' => "default-src 'none'; style-src 'unsafe-inline'",
        ];

        if ($request->boolean('download')) {
            $headers['Content-Disposition'] = 'attachment; filename="qr-'.$link->alias.'.svg"';
        }

        return response(QrCodeGenerator::svg($link->shortUrl()), 200, $headers);
    }
}
