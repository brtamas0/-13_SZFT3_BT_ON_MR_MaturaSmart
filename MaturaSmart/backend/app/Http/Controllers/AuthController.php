<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

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
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sikeres kijelentkezés']);
    }

    public function register(Request $request)
    {
        $fields = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $cleanName = Str::squish($value);
                    
                    if (!preg_match('/^[\p{L}\s\.]+$/u', $cleanName)) {
                        $fail('A név csak betűket tartalmazhat!');
                    }

                    if (!str_contains($cleanName, ' ')) {
                        $fail('Kérlek add meg a teljes nevedet (legalább 2 szó)!');
                    }
                },
            ],
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|string|confirmed|min:6' 
        ]);

        $user = User::create([
            'full_name' => Str::squish($fields['full_name']),
            'email' => $fields['email'],
            'password' => Hash::make($fields['password']),
            'xp' => 0,
            'level' => 1,
            'role' => 'student'
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres regisztráció!',
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        $key = 'profile-update:' . $user->id;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            $minutes = ceil($seconds / 60);
            
            return response()->json([
                'message' => "Túl gyakori módosítás! Kérlek várj még {$minutes} percet."
            ], 429);
        }

        $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $cleanName = Str::squish($value);
                    
                    if (!preg_match('/^[\p{L}\s\.]+$/u', $cleanName)) {
                        $fail('A név csak betűket tartalmazhat!');
                    }

                    if (!str_contains($cleanName, ' ')) {
                        $fail('Kérlek add meg a teljes nevedet (legalább 2 szó)!');
                    }
                },
            ],
        ]);

        $user->update([
            'full_name' => Str::squish($request->full_name)
        ]);
        // Limitáljuk a profil frissítést 5 percenként egy alkalomra
        RateLimiter::hit($key, 5 * 60);

        return response()->json([
            'message' => 'Profil sikeresen frissítve!',
            'user' => $user
        ]);
    }
}