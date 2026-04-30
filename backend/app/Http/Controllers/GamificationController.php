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

        // Hacky auto-auth for MVP: Get or create first user.
        $user = User::firstOrCreate(
            ['email' => 'student@signedu.com'],
            ['name' => 'Demo Student', 'password' => bcrypt('password')]
        );

        $user->xp += $request->xp_earned;
        
        // Simple level logic (every 100 XP is a level)
        $newLevel = floor($user->xp / 100) + 1;
        if($newLevel > $user->level) {
            $user->level = $newLevel;
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
