<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // Profil adatok lekérése
    public function show(Request $request)
    {
        $user = $request->user();

        $nextLevelXp = $user->level * 100;
        $progress = min(100, round(($user->xp / $nextLevelXp) * 100));

        return response()->json([
            'user' => $user,
            'stats' => [
                'next_level_xp' => $nextLevelXp,
                'level_progress' => $progress,
                'completed_topics' => DB::table('question_user')
                    ->where('user_id', $user->id)
                    ->where('is_correct', true)
                    ->count(),
            ]
        ]);
    }

    // Adatok frissítése
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'graduation_year' => 'nullable|integer|min:2024|max:2030',
        ]);

        $user->update($validated);

        return response()->json(['message' => 'Profil sikeresen frissítve!', 'user' => $user]);
    }

    // Jelszó csere
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'A jelenlegi jelszó megadása kötelező.',
            'new_password.required' => 'Az új jelszó megadása kötelező.',
            'new_password.min' => 'Az új jelszónak legalább 8 karakter hosszúnak kell lennie!',
            'new_password.confirmed' => 'Az új jelszavak nem egyeznek meg.',
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'A jelenlegi jelszó hibás.'], 422);
        }

        $user->update(['password' => Hash::make($request->new_password)]);

        return response()->json(['message' => 'Jelszó sikeresen megváltoztatva!']);
    }

}