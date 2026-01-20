<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AxelController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIKUS ROUTE-OK (Bejelentkezés nélkül elérhető)
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:5,1')->group(function () { // Limitáljuk a kéréseket 10 per perc
    // Bejelentkezés
    Route::post('/login', [AuthController::class, 'login']);
    //Regisztráció
    Route::post('/register', [AuthController::class, 'register']);
    

});
/*
|--------------------------------------------------------------------------
| VÉDETT ROUTE-OK (Csak Tokennel érhető el)
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    //Felhasználói adatok
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    //Kijelentkezés
    Route::post('/logout', [AuthController::class, 'logout']);

    //Tartalom (Tantárgyak és Témák)
    Route::get('/tantargyak', [SubjectController::class, 'index']);
    Route::get('/tantargyak/{slug}', [SubjectController::class, 'show']);
    Route::get('/topics/{slug}', [TopicController::class, 'show']);

    //Gamifikáció (XP szerzés)
    Route::post('/gamification/complete-topic', [GamificationController::class, 'completeTopic']);

    //Axel AI
    Route::post('/ask-axel', [AxelController::class, 'ask']);

    //Felhasználói profil frissítése
    Route::get('/user/profile', function (Request $request) {
        return $request->user();
    });
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    
    //Leaderboard
    Route::get('/leaderboard', [GamificationController::class, 'leaderboard']);

    //Témák lekérése tantárgyanként
    Route::get('/topics/{subject}/{topic}', [TopicController::class, 'show']);

    // Profil kezelése
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    // Dashboard adatok lekérése
    Route::get('/dashboard', [DashboardController::class, 'index']);
});