<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AiLog;
use App\Models\VideoTutorial;
use App\Models\SignDictionary;
use App\Models\Quiz;
use App\Models\SystemLog;
use App\Models\LearningClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->role !== 'user') {
            // JIKA ROLE ADMIN / CONTENT MANAGER
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
        } else {
            // USER DASHBOARD LOGIC
            $completedSigns = DB::table('learning_progress')
                ->where('user_id', $user->id)
                ->where('is_completed', true)
                ->count();

            // Fetch Classes instead of just Quizzes
            $classes = LearningClass::with(['quizzes'])->get();
            
            // Get User's Highest Scores per Quiz
            $quizScores = DB::table('quiz_results')
                ->where('user_id', $user->id)
                ->select('quiz_id', DB::raw('MAX(score) as max_score'))
                ->groupBy('quiz_id')
                ->pluck('max_score', 'quiz_id');

            // Class Progress Calculation
            foreach ($classes as $class) {
                $quizIds = $class->quizzes->pluck('id');
                $passedQuizzes = 0;
                foreach ($quizIds as $qid) {
                    if (isset($quizScores[$qid]) && $quizScores[$qid] >= 70) {
                        $passedQuizzes++;
                    }
                }
                
                $practicalCount = DB::table('ai_logs')
                    ->where('user_id', $user->id)
                    ->where('is_correct', true)
                    ->where('sign_detected', 'like', $class->category . '%')
                    ->count();
                
                $class->is_completed = ($passedQuizzes >= $class->quizzes->count() && $practicalCount >= $class->required_practical_count);
                $class->passed_quizzes = $passedQuizzes;
                $class->practical_count = $practicalCount;
            }

            $recentTutorials = VideoTutorial::where('is_published', true)->latest()->take(3)->get();
            
            // XP didapat minggu ini
            $weeklyXp = \App\Models\QuizResult::where('user_id', $user->id)
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->sum('xp_gained');
            
            // Milestone check
            $milestonePopup = null;
            if ($user->xp >= 100000 && $user->last_milestone < 2) {
                $milestonePopup = 'pro';
                $user->update(['last_milestone' => 2]);
            } elseif ($user->xp >= 10000 && $user->last_milestone < 1) {
                $milestonePopup = 'intermediate';
                $user->update(['last_milestone' => 1]);
            }

            return view('admin.dashboard_user', compact(
                'user',
                'completedSigns',
                'classes',
                'quizScores',
                'recentTutorials',
                'weeklyXp',
                'milestonePopup'
            ));
        }
    }

    public function viewCertificate($classId)
    {
        $user = Auth::user();
        $class = LearningClass::findOrFail($classId);
        
        // Safety check: ensure they actually finished it
        $quizScores = DB::table('quiz_results')
            ->where('user_id', $user->id)
            ->whereIn('quiz_id', $class->quizzes->pluck('id'))
            ->select('quiz_id', DB::raw('MAX(score) as max_score'))
            ->groupBy('quiz_id')
            ->pluck('max_score', 'quiz_id');
            
        $passedQuizzes = 0;
        foreach ($class->quizzes as $q) {
            if (isset($quizScores[$q->id]) && $quizScores[$q->id] >= 70) $passedQuizzes++;
        }
        
        $practicalCount = DB::table('ai_logs')
            ->where('user_id', $user->id)
            ->where('is_correct', true)
            ->where('sign_detected', 'like', $class->category . '%')
            ->count();
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
        $aiStatus = false;
        try {
            $ch = curl_init('http://localhost:8069/health');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            $aiStatus = $httpCode === 200;
        } catch (\Exception $e) { $aiStatus = false; }

        $dbStatus = false;
        try { DB::connection()->getPdo(); $dbStatus = true; } catch (\Exception $e) { $dbStatus = false; }

        return response()->json([
            'laravel' => $laravelStatus,
            'ai_engine' => $aiStatus,
            'database' => $dbStatus,
            'memory' => round(memory_get_usage(true) / 1024 / 1024, 1) . ' MB',
            'php_version' => PHP_VERSION,
        ]);
    }

    public function reports()
    {
        $totalUsers = User::where('role', 'user')->count();
        $activeUsers = User::where('role', 'user')->where('xp', '>', 0)->count();
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

        return view('admin.reports', compact('totalUsers', 'activeUsers', 'avgLevel', 'totalAiCalls', 'topSigns', 'levelDistribution'));
    }
}
