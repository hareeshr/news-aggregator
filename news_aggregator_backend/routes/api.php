<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\NewsController;


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

Route::middleware('auth:sanctum')->get('/top-headlines', [NewsController::class, 'getTopHeadlinesNewsAPI']);
Route::middleware('auth:sanctum')->get('/top-articles', [NewsController::class, 'getTopArticlesNYTimesAPI']); //
Route::middleware('auth:sanctum')->get('/top-news', [NewsController::class, 'getLatestNewsGuardian']); //
Route::middleware('auth:sanctum')->get('/combined-news', [NewsController::class, 'getCombinedNews']); //
