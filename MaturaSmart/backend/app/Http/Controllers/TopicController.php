<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;
use App\Models\Subject;

class TopicController extends Controller
{
    public function show(Request $request, $subjectSlug, $topicSlug)
{
    $user = $request->user();

    // Tantárgy, téma lekérése
    $subject = Subject::where('slug', $subjectSlug)->firstOrFail();
    
    $topic = Topic::where('subject_id', $subject->id)
                  ->where('slug', $topicSlug)
                  ->with(['unit', 'flashcards', 'questions.answers'])
                  ->firstOrFail();

    //NAVIGÁCIÓ
    
    // Lekérjük az összes témát a tárgyon belül
    $allTopics = Topic::select('topics.id', 'topics.slug', 'topics.title', 'units.order as unit_order', 'topics.year', 'topics.order')
        ->join('units', 'topics.unit_id', '=', 'units.id')
        ->where('topics.subject_id', $subject->id)
        ->orderBy('units.order', 'asc') // Először Unit szerint
        ->orderByRaw('ISNULL(topics.year), topics.year ASC') // Aztán évszám szerint
        ->orderBy('topics.order', 'asc') // Végül manuális sorrend
        ->get();

    // Mostani index
    $currentIndex = $allTopics->search(function($item) use ($topic) {
        return $item->id === $topic->id;
    });

    // Előző és Következő téma
    $prevTopic = ($currentIndex > 0) ? $allTopics[$currentIndex - 1] : null;
    $nextTopic = ($currentIndex < $allTopics->count() - 1) ? $allTopics[$currentIndex + 1] : null;

    return response()->json([
        'id' => $topic->id,
        'title' => $topic->title,
        'description' => $topic->description,
        'content' => $topic->content,
        'flashcards' => $topic->flashcards,
        'questions' => $topic->questions->map(function($q) {
             return $q;
        }),
        'unit' => $topic->unit,
        
        'prev_slug' => $prevTopic ? $prevTopic->slug : null,
        'next_slug' => $nextTopic ? $nextTopic->slug : null,
        'next_title' => $nextTopic ? $nextTopic->title : null,
    ]);
}
}