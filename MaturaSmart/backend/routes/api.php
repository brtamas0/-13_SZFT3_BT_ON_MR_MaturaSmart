<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\GamificationController;
use App\Http\Controllers\AxelController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;

/*
|--------------------------------------------------------------------------
| PUBLIKUS ROUTE-OK
|--------------------------------------------------------------------------
*/

Route::middleware('throttle:60,1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
});

/*
|--------------------------------------------------------------------------
| VÉDETT ROUTE-OK
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    
    // --- User ---
    Route::get('/user', function (Request $request) { return $request->user(); });
    Route::post('/logout', [AuthController::class, 'logout']);

    // --- Student App ---
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/tantargyak', [SubjectController::class, 'index']);
    Route::get('/tantargyak/{slug}', [SubjectController::class, 'show']);
    Route::get('/topics/{slug}', [TopicController::class, 'show']);
    Route::get('/topics/{subject}/{topic}', [TopicController::class, 'show']);
    Route::post('/ask-axel', [AxelController::class, 'ask']);
    Route::post('/gamification/complete-topic', [GamificationController::class, 'completeTopic']);
    Route::get('/leaderboard', [GamificationController::class, 'leaderboard']);
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile/update', [ProfileController::class, 'update']);
    Route::put('/profile/password', [ProfileController::class, 'updatePassword']);

    /*
    |--------------------------------------------------------------------------
    | ADMIN ROUTE-OK
    |--------------------------------------------------------------------------
    */
    Route::middleware('admin')->prefix('admin')->group(function () {
        
        Route::post('/topics/reorder', [AdminController::class, 'reorderTopics']);

        // 1. Dashboard & User lista
        Route::get('/stats', [AdminController::class, 'stats']);
        Route::get('/users', [AdminController::class, 'indexUsers']);
        Route::post('/verify-password', [AdminController::class, 'verifyPassword']);

        // 2. Tantárgyak
        Route::get('/subjects', [AdminController::class, 'indexSubjects']);
        Route::get('/subjects/{subject}', [AdminController::class, 'showSubject']);
        Route::post('/subjects', [AdminController::class, 'storeSubject']);
        Route::delete('/subjects/{subject}', [AdminController::class, 'destroySubject']);

        // 3. Unitok (Mappák)
        Route::get('/subjects/{subject}/units', [AdminController::class, 'getUnits']);
        Route::post('/subjects/{subject}/units', [AdminController::class, 'storeUnit']);
        Route::delete('/units/{unit}', [AdminController::class, 'destroyUnit']);

        // 4. Topics (Leckék)
        Route::get('/units/{unit}/topics', [AdminController::class, 'getTopics']);
        Route::post('/units/{unit}/topics', [AdminController::class, 'storeTopic']);
        
        // Ez a dinamikus ID-s route most már nem zavarja a reorder-t
        Route::put('/topics/{topic}', [AdminController::class, 'updateTopic']); 
        Route::delete('/topics/{topic}', [AdminController::class, 'destroyTopic']);

        // 5. Kvíz & Flashcard (Course Builderhez)
        Route::get('/topics/{topic}/questions', [AdminController::class, 'getQuestions']);
        Route::post('/topics/{topic}/questions', [AdminController::class, 'storeQuestion']);
        Route::delete('/questions/{question}', [AdminController::class, 'destroyQuestion']);

        Route::get('/topics/{topic}/flashcards', [AdminController::class, 'getFlashcards']);
        Route::post('/topics/{topic}/flashcards', [AdminController::class, 'storeFlashcard']);
        Route::delete('/flashcards/{flashcard}', [AdminController::class, 'destroyFlashcard']);

    });

});