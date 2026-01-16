<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AxelController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\TopicController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/ask-axel', [AxelController::class, 'ask']);

Route::get('/tantargyak', [SubjectController::class, 'index']);
Route::get('/tantargyak/{slug}', [SubjectController::class, 'show']);
Route::get('/topics/{slug}', [TopicController::class, 'show']);