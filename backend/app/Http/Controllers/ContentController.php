<?php

namespace App\Http\Controllers;

use App\Models\VideoTutorial;
use App\Models\SignDictionary;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Http\Request;

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
        $query = Quiz::withCount('questions')->with('creator');

        if ($request->filled('search')) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        $quizzes = $query->latest()->paginate(12)->withQueryString();
        return view('admin.content.quizzes', compact('quizzes'));
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
