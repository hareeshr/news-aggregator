<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PreferenceController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login'])->name('login');
Route::get('/home-articles', [NewsController::class, 'getHomeArticles']);
Route::get('/categories', [NewsController::class, 'getCategories']);
Route::get('/search', [NewsController::class, 'search']);



Route::middleware('auth:sanctum')->get('/user/details', [UserController::class, 'getUserDetails']); //
Route::middleware('auth:sanctum')->post('/user/preferences', [PreferenceController::class, 'saveUserPreferences']); //
Route::middleware('auth:sanctum')->get('/user/preferences', [PreferenceController::class, 'getUserPreferences']); //
Route::middleware('auth:sanctum')->get('/personalized-articles', [NewsController::class, 'getPersonalizedArticles']); //
