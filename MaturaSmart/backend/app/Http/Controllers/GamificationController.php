<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class GamificationController extends Controller
{
    // --- RANGLISTA LEKÉRÉSE ---
    public function leaderboard(Request $request)
    {
        // Top 50 felhasználó XP alapján
        $leaderboard = User::select('id', 'full_name', 'xp', 'level')
            ->orderBy('xp', 'desc')
            ->take(50)
            ->get();

        $currentUser = $request->user();
        
        $rank = User::where('xp', '>', $currentUser->xp)->count() + 1;

        return response()->json([
            'leaderboard' => $leaderboard,
            'user_rank' => $rank,
            'user_xp' => $currentUser->xp
        ]);
    }

    // --- Témakör befejezés pontozás ---
    public function completeTopic(Request $request)
    {
        $user = $request->user();
        $topicId = $request->input('topic_id');
        $answers = $request->input('answers'); 

        $topic = Topic::findOrFail($topicId);
        $questions = Question::where('topic_id', $topicId)->with('answers')->get();

        $xpGained = 0;
        $correctCount = 0;
        $alreadySolvedCount = 0;

        foreach ($questions as $question) {
            $userAnswerId = $answers[$question->id] ?? null;
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            $isCorrectNow = false;

            if ($userAnswerId && $correctAnswer && $userAnswerId == $correctAnswer->id) {
                $isCorrectNow = true;
                $correctCount++;

                // Megnézzük, kapott-e már érte pontot régen
                $alreadySolved = DB::table('question_user')
                    ->where('user_id', $user->id)
                    ->where('question_id', $question->id)
                    ->where('is_correct', true)
                    ->exists();

                if ($alreadySolved) {
                    $alreadySolvedCount++;
                } else {
                    $xpGained += $question->xp;
                }
            }

            // Eredmény mentése
            DB::table('question_user')->updateOrInsert(
                ['user_id' => $user->id, 'question_id' => $question->id],
                ['is_correct' => $isCorrectNow, 'updated_at' => now()]
            );
        }

        if ($xpGained > 0) {
            $user->increment('xp', $xpGained);
        }
        
        $user->update(['last_topic_id' => $topic->id]);

        $message = "";
        if ($xpGained > 0) {
            $message = "Szép munka! Szereztél {$xpGained} XP-t!";
        } elseif ($correctCount > 0 && $alreadySolvedCount > 0) {
            $message = "Hibátlan, de ezekért már kaptál pontot korábban!";
        } else {
            $message = "Gyakorlás befejezve.";
        }

        return response()->json([
            'message' => $message,
            'xp_gained' => $xpGained,
            'total_xp' => $user->xp,
        ]);
    }
}