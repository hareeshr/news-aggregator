<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\UserController;


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
Route::get('/getHomeArticles', [NewsController::class, 'getHomeArticles']);
Route::get('/getCategories', [NewsController::class, 'getCategories']);



Route::middleware('auth:sanctum')->get('/user/details', [UserController::class, 'getUserDetails']); //
Route::middleware('auth:sanctum')->post('/user/preferences', [UserController::class, 'saveUserPreferences']); //
Route::middleware('auth:sanctum')->get('/user/preferences', [UserController::class, 'getUserPreferences']); //
// Route::middleware('auth:sanctum')->get('/combined-news', [NewsController::class, 'getCombinedNews']); //
