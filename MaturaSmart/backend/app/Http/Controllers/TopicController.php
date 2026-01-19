<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function show($subjectSlug, $topicSlug)
    {
        $topic = Topic::where('slug', $topicSlug)
                ->with(['questions.answers', 'subject', 'flashcards'])
                ->firstOrFail();

        return response()->json($topic);
    }
}