<?php

namespace App\Http\Controllers;

use App\Models\AiLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiMonitorController extends Controller
{
    public function index(Request $request)
    {
        $totalLogs = AiLog::count();
        $accuracy = $totalLogs > 0
            ? round(AiLog::where('is_correct', true)->count() / $totalLogs * 100, 1)
            : 0;
        $avgResponseTime = round(AiLog::avg('response_time_ms') ?? 0);
        $todayLogs = AiLog::whereDate('created_at', Carbon::today())->count();

        $query = AiLog::with('user')->latest();

        if ($request->filled('sign')) {
            $query->where('sign_detected', $request->sign);
        }

        $logs = $query->paginate(20)->withQueryString();

        $signAccuracy = AiLog::select(
            'sign_detected',
            DB::raw('COUNT(*) as total'),
            DB::raw('SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct'),
            DB::raw('ROUND(AVG(confidence) * 100, 1) as avg_confidence'),
            DB::raw('ROUND(AVG(response_time_ms)) as avg_response')
        )
            ->groupBy('sign_detected')
            ->orderByDesc('total')
            ->take(15)
            ->get();

        return view('admin.ai-monitor', compact(
            'totalLogs',
            'accuracy',
            'avgResponseTime',
            'todayLogs',
            'logs',
            'signAccuracy'
        ));
    }
}
