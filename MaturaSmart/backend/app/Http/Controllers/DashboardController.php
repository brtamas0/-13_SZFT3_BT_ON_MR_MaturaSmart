<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Subject;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $lastTopicData = null;
        if ($user->last_topic_id) {
            $topic = Topic::with('subject')->find($user->last_topic_id);
            
            if ($topic) {
                $totalQs = DB::table('questions')->where('topic_id', $topic->id)->count();
                
                $solvedQs = DB::table('question_user')
                    ->where('user_id', $user->id)
                    ->whereIn('question_id', function($query) use ($topic) {
                        $query->select('id')->from('questions')->where('topic_id', $topic->id);
                    })
                    ->where('is_correct', true)
                    ->distinct('question_id')
                    ->count();

                $topicProgress = $totalQs > 0 ? round(($solvedQs / $totalQs) * 100) : 0;

                $lastTopicData = [
                    'title' => $topic->title,
                    'slug' => $topic->slug,
                    'progress' => $topicProgress,
                    'subject' => [
                        'title' => $topic->subject->name, 
                        'slug' => $topic->subject->slug
                    ]
                ];
            }
        }

        $subjects = Subject::with(['topics.questions'])->get()->map(function ($subject) use ($user) {
            $topics = $subject->topics;
            $totalTopicsCount = $topics->count(); 
            $completedTopicsCount = 0;

            foreach ($topics as $topic) {
                $questions = $topic->questions;
                $totalQsInTopic = $questions->count();

                if ($totalQsInTopic > 0) {
                    $solvedInTopic = DB::table('question_user')
                        ->where('user_id', $user->id)
                        ->whereIn('question_id', $questions->pluck('id'))
                        ->where('is_correct', true)
                        ->distinct('question_id')
                        ->count();
                    if ($solvedInTopic >= $totalQsInTopic) {
                        $completedTopicsCount++;
                    }
                }
            }
            $progress = $totalTopicsCount > 0 
                ? round(($completedTopicsCount / $totalTopicsCount) * 100) 
                : 0;

            $colors = $this->getSubjectColors($subject->id);

            return [
                'id' => $subject->id,
                'title' => $subject->name, 
                'slug' => $subject->slug,
                'progress' => $progress, 
                'total_topics' => $totalTopicsCount,
                'visuals' => [
                    'icon' => $subject->icon ?? '📘', 
                    'color' => $colors['color'],
                    'bg' => $colors['bg'],
                    'bar_color' => $colors['bar_color']
                ]
            ];
        });

        return response()->json([
            'user' => $user,
            'last_topic' => $lastTopicData,
            'subjects' => $subjects,
            'quote' => "A tudás hatalom."
        ]);
    }

    private function getSubjectColors($id)
    {
        $palettes = [
            ['bg' => 'bg-blue-500/20', 'color' => 'text-blue-400', 'bar_color' => 'bg-blue-500'],
            ['bg' => 'bg-purple-500/20', 'color' => 'text-purple-400', 'bar_color' => 'bg-purple-500'],
            ['bg' => 'bg-green-500/20', 'color' => 'text-green-400', 'bar_color' => 'bg-green-500'],
            ['bg' => 'bg-yellow-500/20', 'color' => 'text-yellow-400', 'bar_color' => 'bg-yellow-500'],
            ['bg' => 'bg-red-500/20', 'color' => 'text-red-400', 'bar_color' => 'bg-red-500'],
            ['bg' => 'bg-pink-500/20', 'color' => 'text-pink-400', 'bar_color' => 'bg-pink-500'],
            ['bg' => 'bg-cyan-500/20', 'color' => 'text-cyan-400', 'bar_color' => 'bg-cyan-500'],
            ['bg' => 'bg-indigo-500/20', 'color' => 'text-indigo-400', 'bar_color' => 'bg-indigo-500'],
        ];

        return $palettes[$id % count($palettes)];
    }
}