<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\SignDictionary;
use App\Models\VideoTutorial;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\AiLog;
use App\Models\SystemLog;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // ── Admin Users ──
        $admin = User::create([
            'name' => 'Admin SignEdu',
            'email' => 'admin@signedu.com',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
            'xp' => 0,
            'level' => 1,
        ]);

        User::create([
            'name' => 'Content Manager',
            'email' => 'content@signedu.com',
            'password' => bcrypt('content123'),
            'role' => 'content_manager',
            'xp' => 0,
            'level' => 1,
        ]);

        // ── Sample Users (spread over 30 days) ──
        $names = ['Andi Pratama','Budi Santoso','Citra Dewi','Dian Saputra','Eka Putri','Fajar Rahman','Gita Lestari','Hadi Wijaya','Indah Sari','Joko Susanto','Kartika Wulandari','Lukman Hakim','Maya Anggraini','Nanda Kurniawan','Olivia Tan','Putra Mahendra','Qori Aisyah','Rizky Aditya','Sinta Maharani','Teguh Prabowo','Umar Fauzi','Vina Oktavia','Wahyu Nugroho','Xena Permata','Yudi Setiawan','Zahra Amelia','Arif Budiman','Bella Safitri','Cahya Firmansyah','Dewi Anggraeni'];

        foreach ($names as $i => $name) {
            $daysAgo = rand(0, 29);
            $xp = rand(0, 800);
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@gmail.com',
                'password' => bcrypt('password'),
                'role' => 'user',
                'xp' => $xp,
                'level' => floor($xp / 100) + 1,
                'streak' => rand(0, 14),
                'created_at' => Carbon::now()->subDays($daysAgo)->subHours(rand(0, 23)),
                'updated_at' => Carbon::now()->subDays(rand(0, $daysAgo)),
            ]);
        }

        // ── Sign Dictionary ──
        $signs = [
            ['word' => 'Halo', 'category' => 'salam', 'description' => 'Gerakan tangan untuk menyapa'],
            ['word' => 'Terima Kasih', 'category' => 'salam', 'description' => 'Gerakan untuk mengucapkan terima kasih'],
            ['word' => 'Maaf', 'category' => 'salam', 'description' => 'Gerakan untuk meminta maaf'],
            ['word' => 'Tolong', 'category' => 'salam', 'description' => 'Gerakan untuk meminta bantuan'],
            ['word' => 'Iya', 'category' => 'dasar', 'description' => 'Gerakan mengangguk/setuju'],
            ['word' => 'Tidak', 'category' => 'dasar', 'description' => 'Gerakan menggeleng/menolak'],
            ['word' => 'Apa', 'category' => 'tanya', 'description' => 'Kata tanya apa dalam BISINDO'],
            ['word' => 'Siapa', 'category' => 'tanya', 'description' => 'Kata tanya siapa dalam BISINDO'],
            ['word' => 'Makan', 'category' => 'aktivitas', 'description' => 'Gerakan makan'],
            ['word' => 'Minum', 'category' => 'aktivitas', 'description' => 'Gerakan minum'],
        ];

        foreach ($signs as $sign) {
            SignDictionary::create(array_merge($sign, ['created_by' => $admin->id]));
        }

        // ── Video Tutorials ──
        $tutorials = [
            ['title' => 'Pengenalan BISINDO', 'category' => 'dasar', 'difficulty' => 'beginner', 'is_published' => true],
            ['title' => 'Alfabet BISINDO A-M', 'category' => 'alfabet', 'difficulty' => 'beginner', 'is_published' => true],
            ['title' => 'Alfabet BISINDO N-Z', 'category' => 'alfabet', 'difficulty' => 'beginner', 'is_published' => true],
            ['title' => 'Sapaan Sehari-hari', 'category' => 'percakapan', 'difficulty' => 'intermediate', 'is_published' => true],
            ['title' => 'Angka 1-20', 'category' => 'angka', 'difficulty' => 'beginner', 'is_published' => false],
        ];

        foreach ($tutorials as $t) {
            VideoTutorial::create(array_merge($t, [
                'video_url' => 'https://www.youtube.com/watch?v=example',
                'description' => 'Tutorial ' . $t['title'],
                'created_by' => $admin->id,
            ]));
        }

        // ── Quizzes (12 Quizzes for Demo) ──
        $categories = ['salam', 'alfabet', 'angka', 'harian'];
        $difficulties = ['beginner', 'intermediate', 'advanced'];

        foreach ($categories as $cat) {
            foreach ($difficulties as $diff) {
                $quiz = Quiz::create([
                    'title' => 'Kuis ' . ucfirst($cat) . ' (' . ucfirst($diff) . ')',
                    'description' => 'Uji kemampuan isyarat ' . $cat . ' di tingkat ' . $diff . '.',
                    'difficulty' => $diff,
                    'category' => $cat,
                    'is_published' => true,
                    'created_by' => $admin->id,
                ]);

                // Create 3 questions per quiz
                for ($k = 1; $k <= 3; $k++) {
                    QuizQuestion::create([
                        'quiz_id' => $quiz->id,
                        'question_text' => 'Pilih isyarat yang tepat untuk "' . ucfirst($cat) . ' ' . $k . '"?',
                        'image_url' => 'https://placehold.co/400x300/16161a/00ff88?text=' . ucfirst($cat) . '+' . $k,
                        'correct_answer' => 'Opsi A',
                        'options' => ['Opsi A', 'Opsi B', 'Opsi C', 'Opsi D'],
                    ]);
                }
            }
        }

        // ── AI Logs (spread over 14 days) ──
        $signNames = ['Halo', 'Terima Kasih', 'Maaf', 'Tolong', 'Iya', 'Tidak', 'Apa', 'Makan'];
        $users = User::where('role', 'user')->pluck('id')->toArray();

        for ($i = 0; $i < 200; $i++) {
            $isCorrect = rand(1, 100) <= 82; // ~82% accuracy
            AiLog::create([
                'user_id' => $users[array_rand($users)],
                'sign_detected' => $signNames[array_rand($signNames)],
                'confidence' => $isCorrect ? rand(70, 98) / 100 : rand(20, 65) / 100,
                'is_correct' => $isCorrect,
                'response_time_ms' => rand(45, 320),
                'created_at' => Carbon::now()->subDays(rand(0, 13))->subHours(rand(0, 23)),
            ]);
        }

        // ── System Logs ──
        $sysLogs = [
            ['level' => 'info', 'message' => 'Server started successfully', 'source' => 'laravel'],
            ['level' => 'info', 'message' => 'AI Engine connected on port 8069', 'source' => 'ai_engine'],
            ['level' => 'info', 'message' => 'Database migration completed', 'source' => 'system'],
            ['level' => 'warning', 'message' => 'AI response time exceeded 200ms threshold', 'source' => 'ai_engine'],
            ['level' => 'warning', 'message' => 'Memory usage at 75%', 'source' => 'system'],
            ['level' => 'info', 'message' => 'Cache cleared successfully', 'source' => 'laravel'],
            ['level' => 'error', 'message' => 'Failed to connect to AI engine (timeout)', 'source' => 'ai_engine'],
            ['level' => 'info', 'message' => 'New user registration: demo@test.com', 'source' => 'auth'],
            ['level' => 'info', 'message' => 'Backup completed: signedu_8bit.sql', 'source' => 'system'],
            ['level' => 'warning', 'message' => 'Rate limit reached for IP 192.168.1.100', 'source' => 'api'],
        ];

        foreach ($sysLogs as $j => $log) {
            SystemLog::create(array_merge($log, [
                'created_at' => Carbon::now()->subHours($j * 3),
            ]));
        }
    }
}
