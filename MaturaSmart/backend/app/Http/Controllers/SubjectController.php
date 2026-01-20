<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SubjectController extends Controller
{
    public function index()
    {
        return Subject::all();
    }

    public function show(Request $request, $slug)
    {
        $user = $request->user();

        // Tantárgyak lekérdezése
        $subject = Subject::where('slug', $slug)->firstOrFail();

        // Unit, Topic lekérdezése
        $units = Unit::where('subject_id', $subject->id)
            ->with(['topics' => function($query) {
                $query->orderByRaw('ISNULL(year), year ASC')->orderBy('order', 'asc');
            }])
            ->orderBy('order', 'asc')
            ->get();
        $units->transform(function ($unit) use ($user) {
            
            $unit->topics->transform(function ($topic) use ($user) {
                $isCompleted = DB::table('question_user')
                    ->join('questions', 'questions.id', '=', 'question_user.question_id')
                    ->where('questions.topic_id', $topic->id)
                    ->where('question_user.user_id', $user->id)
                    ->where('question_user.is_correct', true)
                    ->exists();

                $topic->is_completed = $isCompleted;
                
                if ($topic->year) {
                    $topic->year_label = $topic->year < 0 
                        ? 'i.e. ' . abs($topic->year) 
                        : $topic->year;
                }

                return $topic;
            });

            return $unit;
        });
        // 4. Statisztikák számítása
        $allTopics = $units->flatMap->topics;
        
        $totalCount = $allTopics->count();
        $completedCount = $allTopics->where('is_completed', true)->count();
        
        $progress = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;

        // 5. Válasz összeállítása
        return response()->json([
            'id' => $subject->id,
            'name' => $subject->name,
            'slug' => $subject->slug,
            'icon' => $subject->icon, 
            'description' => $subject->description,
            
            'units' => $units, 
            
            'stats' => [
                'progress' => $progress,
                'completed' => $completedCount,
                'total' => $totalCount,
                'estimated_hours' => round($totalCount * 0.75), // 45 perc
            ]
        ]);
    }
}