<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\Http\Request;

class TopicController extends Controller
{

    public function show($slug)
    {

        $topic = Topic::where('slug', $slug)
                ->with(['questions.answers', 'subject'])
                ->firstOrFail();

        return $topic;
    }
}