<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorial;
use App\Models\SignDictionary;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ContentController extends Controller
{
    // === Video Tutorials ===

    public function tutorials(Request $request)
    {
        $query = VideoTutorial::with('creator');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $tutorials = $query->latest()->paginate(12)->withQueryString();
        return view('admin.content.tutorials', compact('tutorials'));
    }

    public function storeTutorial(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');

        VideoTutorial::create($validated);

        return redirect('/admin/content/tutorials')->with('success', 'Tutorial berhasil ditambahkan.');
    }

    public function updateTutorial(Request $request, VideoTutorial $tutorial)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'video_url' => 'required|url',
            'thumbnail_url' => 'nullable|url',
            'description' => 'nullable|string',
            'category' => 'required|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        $tutorial->update($validated);

        return redirect('/admin/content/tutorials')->with('success', 'Tutorial berhasil diperbarui.');
    }

    public function destroyTutorial(VideoTutorial $tutorial)
    {
        $tutorial->delete();
        return redirect('/admin/content/tutorials')->with('success', 'Tutorial berhasil dihapus.');
    }

    // === Sign Dictionary ===

    public function dictionary(Request $request)
    {
        $query = SignDictionary::with('creator');

        if ($request->filled('search')) {
            $query->where('word', 'like', "%{$request->search}%");
        }

        $entries = $query->latest()->paginate(15)->withQueryString();
        return view('admin.content.dictionary', compact('entries'));
    }

    public function storeDictionary(Request $request)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'thumbnail_url' => 'nullable|url',
            'category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();

        SignDictionary::create($validated);

        return redirect('/admin/content/dictionary')->with('success', 'Kata berhasil ditambahkan ke kamus.');
    }

    public function updateDictionary(Request $request, SignDictionary $entry)
    {
        $validated = $request->validate([
            'word' => 'required|string|max:255',
            'video_url' => 'nullable|url',
            'thumbnail_url' => 'nullable|url',
            'category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $entry->update($validated);

        return redirect('/admin/content/dictionary')->with('success', 'Data kamus berhasil diperbarui.');
    }

    public function destroyDictionary(SignDictionary $entry)
    {
        $entry->delete();
        return redirect('/admin/content/dictionary')->with('success', 'Data kamus berhasil dihapus.');
    }

    // === Quizzes ===

    public function quizzes(Request $request)
    {
        $query = Quiz::withCount('questions');
        if ($request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        $quizzes = $query->latest()->paginate(10);
        return view('admin.content.quizzes', compact('quizzes'));
    }

    public function playQuiz($id)
    {
        $quiz = Quiz::with('questions')->findOrFail($id);
        
        // Shuffle questions for variety
        $quiz->setRelation('questions', $quiz->questions->shuffle());
        
        return view('admin.content.quiz-play', compact('quiz'));
    }

    public function manageQuestions(Quiz $quiz)
    {
        $questions = $quiz->questions;
        return view('admin.content.quiz-questions', compact('quiz', 'questions'));
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'correct_answer' => 'required|string',
            'options' => 'required|array|min:2',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imageUrl = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('quizzes', 'public');
            $imageUrl = '/storage/' . $path;
        }

        $quiz->questions()->create([
            'question_text' => $validated['question_text'],
            'correct_answer' => $validated['correct_answer'],
            'options' => $validated['options'],
            'image_url' => $imageUrl,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function destroyQuestion($id)
    {
        $question = \App\Models\QuizQuestion::findOrFail($id);
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus!');
    }

    public function submitQuiz(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $score = $request->score; // 0-100
            
            // Check Daily Limit: Max 5 Quizzes for XP
            $todayQuizCount = \App\Models\QuizResult::where('user_id', $user->id)
                ->whereDate('created_at', \Carbon\Carbon::today())
                ->count();

            // Logic: Nilai < 70 tidak lulus. Jika sudah 5 kuis hari ini, XP = 0.
            $passed = $score >= 70;
            $xpGained = ($passed && $todayQuizCount < 5) ? (floor($score / 10) * 5) : 0;

            // Simpan Hasil ke Database
            DB::beginTransaction();
            \App\Models\QuizResult::create([
                'user_id' => $user->id,
                'quiz_id' => $id,
                'score' => $score,
                'xp_gained' => $xpGained,
            ]);

            if ($xpGained > 0) {
                $user->xp += $xpGained;
                // NEW LEVEL FORMULA: 535 XP per Level
                $user->level = floor($user->xp / 535) + 1;
                $user->save();
            }
            DB::commit();

            // Check if user hit milestone for popup trigger
            $showMilestone = null;
            if ($user->xp >= 100000 && $user->last_milestone < 2) {
                $showMilestone = 'pro';
                $user->update(['last_milestone' => 2]);
            } elseif ($user->xp >= 10000 && $user->last_milestone < 1) {
                $showMilestone = 'intermediate';
                $user->update(['last_milestone' => 1]);
            }

            return response()->json([
                'success' => true,
                'passed' => $passed,
                'xp_gained' => $xpGained,
                'daily_limit_reached' => $todayQuizCount >= 5,
                'new_xp' => $user->xp,
                'new_level' => $user->level,
                'milestone' => $showMilestone
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function storeQuiz(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'category' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_published'] = $request->has('is_published');

        Quiz::create($validated);

        return redirect('/admin/content/quizzes')->with('success', 'Kuis berhasil ditambahkan.');
    }

    public function updateQuiz(Request $request, Quiz $quiz)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'difficulty' => 'required|in:beginner,intermediate,advanced',
            'category' => 'required|string',
            'is_published' => 'boolean',
        ]);

        $validated['is_published'] = $request->has('is_published');

        $quiz->update($validated);

        return redirect('/admin/content/quizzes')->with('success', 'Kuis berhasil diperbarui.');
    }

    public function destroyQuiz(Quiz $quiz)
    {
        $quiz->delete();
        return redirect('/admin/content/quizzes')->with('success', 'Kuis berhasil dihapus.');
    }
}
