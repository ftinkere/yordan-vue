<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Tightenco\Ziggy\Ziggy;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::name('api')->group(static function () {
//    Route::get('/csrf-cookie', static function() {
//        return redirect('/sanctum/csrf-cookie');
//    });

    Route::get('/user', static function(Request $request) {
        return $request->user() ?? null;
    });

    Route::name('login')->post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::name('logout')->post('/logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);

    // Languages
    Route::name('lastLanguages')->get('/languages/lasts', [\App\Http\Controllers\Api\LanguagesController::class, 'lastLanguages']);
    Route::name('ownedLanguages')->get('/languages/owned', [\App\Http\Controllers\Api\LanguagesController::class, 'ownedLanguages']);

    // Auth
    Route::middleware('auth:sanctum')->group(static function () {

    });
});