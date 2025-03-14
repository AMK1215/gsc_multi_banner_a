<?php
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Home\HomeController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/player-change-password', [AuthController::class, 'playerChangePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // general
    Route::get('/home', [HomeController::class, 'index']);
    // games
    Route::get('/game_types', [HomeController::class, 'gameTypes']);
    Route::get('/providers/{type}', [HomeController::class, 'providers']);
    Route::get('/game_lists/{type}/{provider}', [HomeController::class, 'gameLists']);
});






