<?php

namespace App\Http\Controllers;

use App\Models\ShortLink;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    public function show(ShortLink $link): View
    {
        Gate::authorize('view', $link);

        $days = (int) config('shortlink.analytics_days', 30);
        $tz = config('app.timezone');
        $start = Carbon::now($tz)->subDays($days - 1)->startOfDay();

        $events = $link->clickEvents();

        $totalClicks = $events->count();
        $clicksInRange = (clone $events)->where('clicked_at', '>=', $start)->pluck('clicked_at');

        // Kelompokkan per tanggal di PHP agar portabel antara MySQL dan SQLite (tes).
        $perDay = $clicksInRange->countBy(fn ($at) => Carbon::parse($at)->timezone($tz)->toDateString());

        $daily = collect(range(0, $days - 1))->mapWithKeys(function ($offset) use ($start, $perDay) {
            $date = $start->copy()->addDays($offset)->toDateString();

            return [$date => $perDay->get($date, 0)];
        });

        $topReferers = $link->clickEvents()
            ->selectRaw("COALESCE(referer_host, '') as host, COUNT(*) as total")
            ->groupBy('host')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $browsers = $link->clickEvents()
            ->selectRaw("COALESCE(user_agent_family, 'Tidak diketahui') as family, COUNT(*) as total")
            ->groupBy('family')
            ->orderByDesc('total')
            ->get();

        $recentClicks = $link->clickEvents()->latest('clicked_at')->limit(20)->get();

        return view('links.analytics', [
            'link' => $link,
            'days' => $days,
            'totalClicks' => $totalClicks,
            'clicksInRange' => $clicksInRange->count(),
            'clicksToday' => $daily->last(),
            'daily' => $daily,
            'topReferers' => $topReferers,
            'browsers' => $browsers,
            'recentClicks' => $recentClicks,
        ]);
    }
}
