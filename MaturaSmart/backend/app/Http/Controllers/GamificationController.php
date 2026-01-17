<?php

namespace App\Http\Controllers;

use App\Models\TopicCompletion;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GamificationController extends Controller
{
    public function completeTopic(Request $request)
    {
        $userId = Auth::id(); 
        
        if (!$userId) {
            return response()->json(['message' => 'Nem vagy bejelentkezve!'], 401);
        }

        $request->validate([
            'topic_id' => 'required|exists:topics,id',
            'percentage' => 'integer|min:0|max:100'
        ]);

        $topicId = $request->topic_id;
        $xpReward = 50;
        $existingCompletion = TopicCompletion::where('user_id', $userId)
                                           ->where('topic_id', $topicId)
                                           ->first();

        if ($existingCompletion) {
            if ($request->percentage > $existingCompletion->score_percentage) {
                $existingCompletion->update(['score_percentage' => $request->percentage]);
            }
            
            $currentXp = User::where('id', $userId)->value('xp');

            return response()->json([
                'success' => true,
                'message' => 'Lecke frissítve (XP már megszerezve).',
                'xp_gained' => 0, 
                'total_xp' => $currentXp,
                'first_time' => false
            ]);
        }

        TopicCompletion::create([
            'user_id' => $userId,
            'topic_id' => $topicId,
            'xp_earned' => $xpReward,
            'score_percentage' => $request->percentage ?? 0
        ]);
        User::where('id', $userId)->increment('xp', $xpReward);
        $newTotalXp = User::where('id', $userId)->value('xp');

        return response()->json([
            'success' => true,
            'message' => 'Lecke teljesítve! Szép munka!',
            'xp_gained' => $xpReward,
            'total_xp' => $newTotalXp,
            'first_time' => true
        ]);
    }

    //toplista
    public function leaderboard(Request $request)
{
    //Top 50
    $topUsers = User::select('id', 'full_name', 'xp', 'level')
        ->orderBy('xp', 'desc')
        ->take(50)
        ->get();

    $currentUser = $request->user();
    $currentRank = User::where('xp', '>', $currentUser->xp)->count() + 1;

    return response()->json([
        'leaderboard' => $topUsers,
        'user_rank' => $currentRank,
        'user_xp' => $currentUser->xp
    ]);
}
}