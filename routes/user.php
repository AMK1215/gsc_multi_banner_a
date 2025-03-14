<?php
use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\PromotionController;
use Illuminate\Support\Facades\Route;







Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

// general unauth
Route::get('promotion', [PromotionController::class, 'index']);
Route::get('/home', [BannerController::class, 'index']);
Route::get('banner_text', [BannerController::class, 'bannerText']);
Route::get('ads-banner', [BannerController::class, 'adsBanner']);
Route::get('banners', [BannerController::class, 'banners']);
Route::get('videoads', [BannerController::class, 'ApiVideoads']);

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::post('/player-change-password', [AuthController::class, 'playerChangePassword']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // general

    Route::get('contact', [ContactController::class, 'contact']);
});






