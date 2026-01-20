<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{
    public function show(Request $request, $subjectSlug, $topicSlug)
    {
        $topic = Topic::where('slug', $topicSlug)
                ->with(['questions.answers', 'subject', 'flashcards'])
                ->firstOrFail();

        if ($request->user()) {
            $request->user()->update(['last_topic_id' => $topic->id]);
        }

        return response()->json($topic);
    }
}