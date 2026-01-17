<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validálás
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Hitelesítés
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Hibás email cím vagy jelszó!'
            ], 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        $user->tokens()->delete();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres bejelentkezés!',
            'token' => $token,
            'user' => $user
        ]);
    }
    
    // Kijelentkezés
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sikeres kijelentkezés']);
    }

    // Regisztráció

    public function register(Request $request)
    {
        // Adatok validálása
        $fields = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6' 
        ]);

        // Felhasználó létrehozása
        $user = User::create([
            'full_name' => $fields['full_name'],
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'xp' => 0,
            'level' => 1,
            'role' => 'student'
        ]);

        // Azonnali beléptetés
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres regisztráció!',
            'user' => $user,
            'token' => $token
        ], 201);
    }

        // Profil frissítése (például a teljes név módosítása)
        public function updateProfile(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
        ]);

        $user = $request->user();
        $user->update([
            'full_name' => $request->full_name
        ]);

        return response()->json([
            'message' => 'Profil sikeresen frissítve!',
            'user' => $user
        ]);
    
    }
}