<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GamificationController extends Controller
{
    /**
     * Update XP when a sign is successfully captured.
     * We're faking auth with User ID 1 for MVP.
     */
    public function updateXp(Request $request)
    {
        $request->validate([
            'xp_earned' => 'required|integer',
            'sign_id' => 'required|string'
        ]);

        // Use authenticated user if session exists, otherwise fallback to first (for AI engine standalone)
        $user = auth('web')->user() ?? User::first();

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }

        $user->xp += $request->xp_earned;
        
        // NEW LEVEL LOGIC: 535 XP per level
        $user->level = floor($user->xp / 535) + 1;

        // Milestone Tracking
        if ($user->xp >= 100000 && $user->last_milestone < 2) {
            $user->last_milestone = 2;
        } elseif ($user->xp >= 10000 && $user->last_milestone < 1) {
            $user->last_milestone = 1;
        }

        $user->save();

        // Mark sign as completed
        DB::table('learning_progress')->updateOrInsert(
            [
                'user_id' => $user->id,
                'module_name' => $request->sign_id
            ],
            [
                'is_completed' => true,
                'updated_at' => now()
            ]
        );

        return response()->json([
            'success' => true,
            'xp' => $user->xp,
            'level' => $user->level,
            'milestone' => $user->last_milestone,
            'message' => 'XP added for ' . $request->sign_id
        ]);
    }

    /**
     * Get User Progress
     */
    public function getProgress()
    {
        $user = User::firstOrCreate(
            ['email' => 'student@signedu.com'],
            ['name' => 'Demo Student', 'password' => bcrypt('password')]
        );

        $completed = DB::table('learning_progress')
                        ->where('user_id', $user->id)
                        ->where('is_completed', true)
                        ->pluck('module_name');

        return response()->json([
            'user' => [
                'name' => $user->name,
                'xp' => $user->xp,
                'level' => $user->level,
                'avatar_8bit' => $user->avatar_8bit ?? 'default.png'
            ],
            'completed_signs' => $completed
        ]);
    }
}
