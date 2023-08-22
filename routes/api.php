<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware(['web', 'auth:sanctum'])->group(static function () {
    Route::name('api.user.update')->post('/user', [UserController::class, 'update']);
    Route::name('api.user.resend_confirmation')->post('/user/resend_confirmation', [UserController::class, 'resend_confirmation']);
    Route::name('api.user.push_avatar')->post('/user/avatar', [UserController::class, 'push_avatar']);
});
