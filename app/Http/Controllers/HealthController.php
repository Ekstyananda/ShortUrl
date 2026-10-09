<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class HealthController extends Controller
{
    public function __invoke(): JsonResponse
    {
        try {
            DB::select('select 1');
            $database = 'ok';
        } catch (Throwable) {
            $database = 'error';
        }

        $ok = $database === 'ok';

        return response()->json([
            'status' => $ok ? 'ok' : 'degraded',
            'database' => $database,
        ], $ok ? 200 : 503);
    }
}
