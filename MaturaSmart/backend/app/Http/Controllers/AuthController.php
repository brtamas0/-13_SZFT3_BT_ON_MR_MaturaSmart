<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // 1. Google Login indítása
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    // 2. Google Visszatérés kezelése
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Van ilyen email címmel user?
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Nincs: Létrehozzuk
                $user = User::create([
                    'full_name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'password' => null,
                    'avatar_url' => $googleUser->getAvatar(),
                    'role' => 'student',
                    'xp' => 0,
                    'level' => 1,
                ]);
            } else {
                // HA VAN: Frissítjük az adatokat (Összekötés)
                $updateData = [];
                
                if (!$user->google_id) {
                    $updateData['google_id'] = $googleUser->getId();
                }
                if (!$user->avatar_url) {
                    $updateData['avatar_url'] = $googleUser->getAvatar();
                }
                
                if (!empty($updateData)) {
                    $user->update($updateData);
                }
            }

            // Token generálás a belépéshez (Sanctum)
            $token = $user->createToken('auth_token')->plainTextToken;

            // Visszairányítás a Frontend oldalra (Vue)
            $frontendUrl = env('FRONTEND_URL', 'http://maturasmart.hu') . "/google-callback";

            // A user adatokat átadjuk az URL-ben --> frontend tudja ki lépett be
            $userData = urlencode(json_encode($user));
            
            return redirect("{$frontendUrl}?token={$token}&user={$userData}");

        } catch (\Exception $e) {
            // Hiba esetén visszaküldjük a login oldalra
            return redirect("http://maturasmart.hu/login?error=google_login_failed");
        }
    }

    // 3. Sima Login
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
        
        // Régi tokenek törlése
        $user->tokens()->delete();
        
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Sikeres bejelentkezés!',
            'token' => $token,
            'user' => $user
        ]);
    }
    
    // 4. Kijelentkezés
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Sikeres kijelentkezés']);
    }

    // 5. Sima Regisztráció
    public function register(Request $request)
    {
        $fields = $request->validate([
            'full_name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    $cleanName = Str::squish($value);
                    
                    // Csak betűk és pont (pl. Dr. Kiss)
                    if (!preg_match('/^[\p{L}\s\.]+$/u', $cleanName)) {
                        $fail('A név csak betűket tartalmazhat!');
                    }

                    // Legalább két szó legyen (Vezetéknév Keresztnév)
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

    // 6. Profil Frissítés (Név)
    public function updateProfile(Request $request)
    {
        $user = $request->user();
        
        // Rate Limiting: userenként külön kulcs
        $key = 'profile-update:' . $user->id;

        // Ha túl sokat próbálkozott (pl. 1 percen belül többször)
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
        
        // Sikeres frissítés után beállítjuk az időkorlátot (5 perc)
        RateLimiter::hit($key, 5 * 60);

        return response()->json([
            'message' => 'Profil sikeresen frissítve!',
            'user' => $user
        ]);
    }
    // 7. Jelszó Emlékeztető Küldése
    public function sendResetLink(Request $request)
    {
        // Validálás: kötelező az email, és léteznie kell a users táblában
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.exists' => 'Ezzel az email címmel nincs regisztrált felhasználó.',
            'email.required' => 'Az email mező kötelező.',
            'email.email' => 'Helytelen email formátum.'
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()->first()], 422);
        }

        // A Laravel beépített jelszókezelőjét használjuk
        $status = Password::sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            return response()->json([
                'status' => 'success',
                'message' => 'Az emlékeztető emailt elküldtük!'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Nem sikerült elküldeni az emailt. Próbáld újra később.'
        ], 500);
    }
}