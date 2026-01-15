<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AxelController;
use App\Http\Controllers\SubjectController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
Route::post('/ask-axel', [AxelController::class, 'ask']);

Route::get('/subjects', [SubjectController::class, 'index']);
Route::get('/subjects/{slug}', [SubjectController::class, 'show']);