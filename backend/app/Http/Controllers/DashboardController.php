<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AiLog;
use App\Models\VideoTutorial;
use App\Models\SignDictionary;
use App\Models\Quiz;
use App\Models\SystemLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalUsers = User::where('role', 'user')->count();
        $newUsersToday = User::where('role', 'user')
            ->whereDate('created_at', Carbon::today())
            ->count();
        $totalContent = VideoTutorial::count() + SignDictionary::count() + Quiz::count();

        $aiAccuracy = AiLog::count() > 0
            ? round(AiLog::where('is_correct', true)->count() / AiLog::count() * 100, 1)
            : 0;

        $avgResponseTime = round(AiLog::avg('response_time_ms') ?? 0);

        $recentUsers = User::where('role', 'user')
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = SystemLog::latest()->take(8)->get();

        $topSigns = AiLog::select('sign_detected', DB::raw('COUNT(*) as total'))
            ->groupBy('sign_detected')
            ->orderByDesc('total')
            ->take(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'newUsersToday',
            'totalContent',
            'aiAccuracy',
            'avgResponseTime',
            'recentUsers',
            'recentLogs',
            'topSigns'
        ));
    }

    public function userGrowthData()
    {
        $data = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $count = User::where('role', 'user')
                ->whereDate('created_at', $date)
                ->count();
            $data[] = [
                'date' => $date->format('d M'),
                'count' => $count,
            ];
        }

        return response()->json($data);
    }

    public function aiAccuracyData()
    {
        $data = [];
        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $total = AiLog::whereDate('created_at', $date)->count();
            $correct = AiLog::whereDate('created_at', $date)->where('is_correct', true)->count();
            $data[] = [
                'date' => $date->format('d M'),
                'accuracy' => $total > 0 ? round($correct / $total * 100, 1) : 0,
                'total' => $total,
            ];
        }

        return response()->json($data);
    }

    public function systemHealth()
    {
        $laravelStatus = true;

        // Check AI engine
        $aiStatus = false;
        try {
            $ch = curl_init('http://localhost:8069/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $aiStatus = $httpCode === 200;
        } catch (\Exception $e) {
            $aiStatus = false;
        }

        // Check DB
        $dbStatus = false;
        try {
            DB::connection()->getPdo();
            $dbStatus = true;
        } catch (\Exception $e) {
            $dbStatus = false;
        }

        return response()->json([
            'laravel' => $laravelStatus,
            'ai_engine' => $aiStatus,
            'database' => $dbStatus,
            'memory' => round(memory_get_usage(true) / 1024 / 1024, 1) . ' MB',
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
        ]);
    }

    public function reports()
    {
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')
            ->where('xp', '>', 0)
            ->count();

        $avgLevel = round(User::where('role', 'user')->avg('level') ?? 0, 1);
        $totalAiCalls = AiLog::count();

        $topSigns = AiLog::select('sign_detected', DB::raw('COUNT(*) as total'))
            ->groupBy('sign_detected')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        $levelDistribution = User::where('role', 'user')
            ->select('level', DB::raw('COUNT(*) as total'))
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        return view('admin.reports', compact(
            'totalUsers',
            'activeUsers',
            'avgLevel',
            'totalAiCalls',
            'topSigns',
            'levelDistribution'
        ));
    }
}
