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
            $topic = Topic::with('subject', 'questions')->find($user->last_topic_id);
            
            if ($topic) {
                $totalQs = $topic->questions->count();
                $solvedQs = DB::table('question_user')
                    ->where('user_id', $user->id)
                    ->whereIn('question_id', $topic->questions->pluck('id'))
                    ->where('is_correct', true)
                    ->count();

                $topicProgress = $totalQs > 0 ? round(($solvedQs / $totalQs) * 100) : 0;

                $lastTopicData = [
                    'title' => $topic->title,
                    'slug' => $topic->slug,
                    'subject' => [
                        'title' => $topic->subject->title,
                        'slug' => $topic->subject->slug
                    ],
                    'progress' => $topicProgress
                ];
            }
        }

        $subjects = Subject::with('topics.questions')->get()->map(function ($subject) use ($user) {
            
            $totalQuestionsInSubject = $subject->topics->flatMap->questions->count();
            
            $questionIds = $subject->topics->flatMap->questions->pluck('id');
            $solvedCount = DB::table('question_user')
                ->where('user_id', $user->id)
                ->whereIn('question_id', $questionIds)
                ->where('is_correct', true)
                ->count();

            $progress = $totalQuestionsInSubject > 0 
                ? round(($solvedCount / $totalQuestionsInSubject) * 100) 
                : 0;

            $visuals = match($subject->slug) {
                'matematika' => [
                    'icon' => '🧮', 
                    'color' => 'text-blue-400', 
                    'bg' => 'bg-blue-500/20', 
                    'bar_color' => 'bg-blue-600'
                ],
                'tortenelem' => [
                    'icon' => '⚔️', 
                    'color' => 'text-orange-400', 
                    'bg' => 'bg-orange-500/20',
                    'bar_color' => 'bg-orange-500' 
                ],
                'irodalom'   => [
                    'icon' => '📖', 
                    'color' => 'text-emerald-400', 
                    'bg' => 'bg-emerald-500/20',
                    'bar_color' => 'bg-emerald-500'
                ],
                'angol'      => [
                    'icon' => '🇬🇧', 
                    'color' => 'text-red-400', 
                    'bg' => 'bg-red-500/20',
                    'bar_color' => 'bg-red-500'
                ],
                'informatika'=> [
                    'icon' => '💻', 
                    'color' => 'text-cyan-400', 
                    'bg' => 'bg-cyan-500/20',
                    'bar_color' => 'bg-cyan-500'
                ],
                default      => [
                    'icon' => '📚', 
                    'color' => 'text-slate-400', 
                    'bg' => 'bg-slate-500/20',
                    'bar_color' => 'bg-slate-500'
                ],
            };

            return [
                'id' => $subject->id,
                'title' => $subject->title, 
                'slug' => $subject->slug,
                'progress' => $progress, 
                'total_topics' => $subject->topics->count(),
                'visuals' => $visuals
            ];
        });

        return response()->json([
            'user' => $user,
            'last_topic' => $lastTopicData,
            'subjects' => $subjects,
            'quote' => "A tudás hatalom."
        ]);
    }
}