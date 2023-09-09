<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::name('main')->get('/', [MainController::class, 'index']);

Route::prefix('auth')->group(static function() {
   Route::name('register')->get('/register', [AuthController::class, 'register'])->middleware('guest');
   Route::post('/register', [AuthController::class, 'register_post'])->middleware('guest');

   Route::name('login')->get('/', [AuthController::class, 'login'])->middleware('guest');
   Route::post('/', [AuthController::class, 'login_post'])->middleware('guest');

   Route::name('cabinet')->get('/cabinet', [AuthController::class, 'cabinet'])->middleware('auth');

   Route::name('logout')->get('/logout', [AuthController::class, 'logout'])->middleware('auth');

   Route::name('verify')->get('/verify', [AuthController::class, 'verify']);
});
Route::name('profile')->get('/profile/{user}', [ProfileController::class, 'profile']);

Route::name('languages')->get('/languages', [LanguagesController::class, 'index']);
Route::name('languages.')->prefix('/languages')->group(static function() {
    Route::name('view')->get('/{code}', [LanguagesController::class, 'view']);
    Route::name('store')->post('', [LanguagesController::class, 'store']);
    Route::name('update')->post('{code}', [LanguagesController::class, 'update']);

    Route::name('action')->get('/{code}/action', [LanguagesController::class, 'action']);
});
