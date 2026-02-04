<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Topic;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SystemMessage;

class AdminController extends Controller
{
    // STATISZTIKA
    public function stats()
    {
        return response()->json([
            'users' => User::count(),
            'subjects' => Subject::count(),
            'topics' => Topic::count(),
        ]);
    }

    // FELHASZNÁLÓK LISTÁZÁSA
    public function indexUsers()
    {
        return User::orderBy('role', 'asc')->orderBy('full_name', 'asc')->get();
    }

    // JELSZÓ ELLENŐRZÉS
    public function verifyPassword(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (Hash::check($request->password, $request->user()->password)) {
            return response()->json(['message' => 'OK']);
        }

        return response()->json(['message' => 'Hibás jelszó'], 403);
    }

    // TANTÁRGYAK
    public function indexSubjects()
    {
        return Subject::withCount('units')->get();
    }

    public function storeSubject(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        return Subject::create($validated);
    }

    public function destroySubject(Subject $subject)
    {
        $subject->delete();
        return response()->json(['message' => 'Törölve']);
    }

    // UNITOK (Mappák)
    public function getUnits(Subject $subject)
    {
        return $subject->units()->orderBy('order', 'asc')->get();
    }

    public function storeUnit(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'order' => 'integer'
        ]);
        return $subject->units()->create($validated);
    }

    public function destroyUnit(Unit $unit)
    {
        $unit->delete();
        return response()->noContent();
    }

    // TOPICS (Leckék és Tesztek) KEZELÉSE
    public function getTopics(Unit $unit)
{
    return $unit->topics()
                ->orderBy('order', 'asc') 
                ->get();
}

    public function storeTopic(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'nullable|in:lesson,test',
            'xp' => 'integer',
            'order' => 'integer',
            'time_limit_minutes' => 'nullable|integer',
            'passing_percentage' => 'nullable|integer',
            'reading_weight' => 'nullable|integer|min:0|max:100', 
        ]);

        // Slug generálás
        $baseSlug = \Illuminate\Support\Str::slug($validated['title']);
        $slug = $baseSlug;
        $counter = 1;

        while (\App\Models\Topic::where('slug', $slug)->exists()) {
            $counter++;
            $slug = $baseSlug . '-' . $counter;
        }
        $validated['slug'] = $slug;

        $validated['subject_id'] = $unit->subject_id;

        if (!isset($validated['type'])) {
            $validated['type'] = 'lesson';
        }
        if (!isset($validated['reading_weight'])) {
            $validated['reading_weight'] = 50;
        }

        return $unit->topics()->create($validated);
    }

    public function updateTopic(Request $request, Topic $topic)
    {
        $topic->update($request->all());
        return $topic;
    }

    public function destroyTopic(Topic $topic)
    {
        $topic->delete();
        return response()->noContent();
    }

    public function showSubject(Subject $subject)
    {
        return $subject;
    }

    // KVÍZ (Kérdések) KEZELÉSE
    public function getQuestions(Topic $topic)
    {
        return $topic->questions()->with('answers')->get();
    }

    public function storeQuestion(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'content' => 'required|string',
            'xp' => 'integer',
            'answers' => 'required|array|min:2',
            'answers.*.text' => 'required|string',
            'answers.*.is_correct' => 'boolean'
        ]);

        // 1. Kérdés mentése
        $question = $topic->questions()->create([
            'content' => $validated['content'],
            'type' => 'multiple_choice',
            'xp' => $validated['xp'] ?? 10,
            'difficulty' => 1
        ]);

        // 2. Válaszok mentése
        foreach ($validated['answers'] as $ans) {
            $question->answers()->create([
                'text' => $ans['text'],
                'is_correct' => $ans['is_correct']
            ]);
        }

        return $question->load('answers');
    }

    public function destroyQuestion(\App\Models\Question $question)
    {
        $question->delete();
        return response()->noContent();
    }

    // FLASHCARDOK KEZELÉSE
    public function getFlashcards(Topic $topic)
    {
        return $topic->flashcards()->get();
    }

    public function storeFlashcard(Request $request, Topic $topic)
    {
        $validated = $request->validate([
            'front' => 'required|string',
            'back' => 'required|string'
        ]);

        return $topic->flashcards()->create($validated);
    }

    public function destroyFlashcard(\App\Models\Flashcard $flashcard)
    {
        $flashcard->delete();
        return response()->noContent();
    }

    // SORRENDEZÉS

    public function reorderTopics(Request $request)
    {
        $validated = $request->validate([
            'unit_id' => 'required|integer|exists:units,id',
            'topics' => 'required|array',
            'topics.*.id' => 'required|integer',
            'topics.*.order' => 'required|integer',
        ]);

        foreach ($validated['topics'] as $item) {
            Topic::where('id', $item['id'])->update([
                'order' => $item['order'],
                'unit_id' => $validated['unit_id']
            ]);
        }

        return response()->json(['message' => 'Sorrend és mappa frissítve']);
    }
    public function sendSystemMessage(Request $request)
{
    $request->validate([
        'title' => 'required|string|max:255',
        'message' => 'required|string',
    ]);

    $users = User::all();

    Notification::send($users, new SystemMessage($request->title, $request->message));

    return response()->json(['message' => 'Rendszerüzenet sikeresen elküldve!']);
}
}