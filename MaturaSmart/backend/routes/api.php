<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AxelController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\AuthController;

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
    
    // 1. Felhasználói adatok
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // 2. Kijelentkezés
    Route::post('/logout', [AuthController::class, 'logout']);

    // 3. Tartalom (Tantárgyak és Témák)
    Route::get('/tantargyak', [SubjectController::class, 'index']);
    Route::get('/tantargyak/{slug}', [SubjectController::class, 'show']);
    Route::get('/topics/{slug}', [TopicController::class, 'show']);

    // 4. Gamifikáció (XP szerzés)
    Route::post('/gamification/complete-topic', [GamificationController::class, 'completeTopic']);

    // 5. Axel AI
    Route::post('/ask-axel', [AxelController::class, 'ask']);
});