<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Topic;
use Illuminate\Support\Facades\DB;

class GamificationController extends Controller
{
    public function leaderboard(Request $request)
    {
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

    public function completeTopic(Request $request)
    {
        $user = $request->user();
        $topicId = $request->input('topic_id');
        $answers = $request->input('answers'); 

        $topic = Topic::findOrFail($topicId);
        $questions = Question::where('topic_id', $topicId)->with('answers')->get();

        // EREDMÉNY SZÁMÍTÁSA
        $totalXpAvailable = 0;
        $earnedXpInThisSession = 0;
        
        foreach ($questions as $question) {
            $totalXpAvailable += $question->xp;
            
            $userAnswerId = $answers[$question->id] ?? null;
            $correctAnswer = $question->answers->where('is_correct', true)->first();

            if ($userAnswerId && $correctAnswer && $userAnswerId == $correctAnswer->id) {
                $earnedXpInThisSession += $question->xp;
            }
        }

        // Százalék számítás
        $percentage = $totalXpAvailable > 0 ? ($earnedXpInThisSession / $totalXpAvailable) * 100 : 0;

        // VIIZSGA ELLENŐRZÉS
        if ($topic->type === 'test') {
            $passing = $topic->passing_percentage ?? 50;
            
            if ($percentage < $passing) {
                // SIKERTELEN: Nem mentünk semmit, 0 XP.
                return response()->json([
                    'message' => "Sajnos a vizsga nem sikerült ({$percentage}%). Próbáld újra a pontokért!",
                    'xp_gained' => 0,
                    'total_xp' => $user->xp,
                    'passed' => false
                ]);
            }
        }

        // 3. LÉPÉS: Siker; XP+mentés
        $xpToGrant = 0;
        $correctCount = 0;
        
        foreach ($questions as $question) {
            $userAnswerId = $answers[$question->id] ?? null;
            $correctAnswer = $question->answers->where('is_correct', true)->first();
            $isCorrectNow = false;

            if ($userAnswerId && $correctAnswer && $userAnswerId == $correctAnswer->id) {
                $isCorrectNow = true;
                $correctCount++;

                // Megnézzük, hogy megoldotta e már korábban helyesen
                $alreadySolved = DB::table('question_user')
                    ->where('user_id', $user->id)
                    ->where('question_id', $question->id)
                    ->where('is_correct', true)
                    ->exists();

                if (!$alreadySolved) {
                    $xpToGrant += $question->xp;
                }
            }

            DB::table('question_user')->updateOrInsert(
                ['user_id' => $user->id, 'question_id' => $question->id],
                ['is_correct' => $isCorrectNow, 'updated_at' => now()]
            );
        }

        // XP jóváírás
        if ($xpToGrant > 0) {
            $user->increment('xp', $xpToGrant);
        }
        
        $user->update(['last_topic_id' => $topic->id]);

        $message = $xpToGrant > 0 
            ? "Gratulálunk! Szereztél {$xpToGrant} XP-t!" 
            : "Sikeres, de ezekért a kérdésekért már kaptál pontot korábban!";

        return response()->json([
            'message' => $message,
            'xp_gained' => $xpToGrant,
            'total_xp' => $user->xp,
            'passed' => true
        ]);
    }
}