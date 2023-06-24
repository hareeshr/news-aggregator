<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PreferenceController;

// Authentication Routes
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');

// News Routes
Route::get('/home-articles', [NewsController::class, 'getHomeArticles']);
Route::get('/categories', [NewsController::class, 'getCategories']);
Route::get('/search', [NewsController::class, 'search']);
Route::middleware('auth:sanctum')->get('/personalized-articles', [NewsController::class, 'getPersonalizedArticles']);

// User Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user/details', [UserController::class, 'getUserDetails']);
    Route::get('/user/preferences', [PreferenceController::class, 'getUserPreferences']);
    Route::post('/user/preferences', [PreferenceController::class, 'saveUserPreferences']);
});
