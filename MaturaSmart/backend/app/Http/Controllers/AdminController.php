<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Topic;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

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
    public function getUnits(Subject $subject) {
        return $subject->units()->orderBy('order')->get();
    }

    public function storeUnit(Request $request, Subject $subject) {
        $validated = $request->validate([
            'title' => 'required|string',
            'order' => 'integer'
        ]);
        return $subject->units()->create($validated);
    }
    
    public function destroyUnit(Unit $unit) {
        $unit->delete();
        return response()->noContent();
    }

    // TOPICS (Leckék)
    public function getTopics(Unit $unit) {
        return $unit->topics()->orderBy('order')->get();
    }

    public function storeTopic(Request $request, Unit $unit) {
        $validated = $request->validate([
            'title' => 'required|string',
            'xp' => 'integer', 
            'order' => 'integer'
        ]);
        
        $validated['slug'] = Str::slug($validated['title']) . '-' . rand(1000,9999);
        $validated['subject_id'] = $unit->subject_id;

        return $unit->topics()->create($validated);
    }

    public function updateTopic(Request $request, Topic $topic)
    {
        $topic->update($request->all());
        return $topic;
    }
    
    public function destroyTopic(Topic $topic) {
        $topic->delete();
        return response()->noContent();
    }

    public function showSubject(Subject $subject)
    {
        return $subject;
    }

    // KVÍZ (Kérdések) KEZELÉSE

    public function getQuestions(Topic $topic) {
        return $topic->questions()->with('answers')->get();
    }

    public function storeQuestion(Request $request, Topic $topic) {
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

    public function destroyQuestion(\App\Models\Question $question) {
        $question->delete();
        return response()->noContent();
    }


}