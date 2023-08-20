<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

Route::name('main')->get('/', static function() {
    return Inertia::render('Main');
});

Route::prefix('auth')->group(static function() {
   Route::name('register')->get('/register', [AuthController::class, 'register'])->middleware('guest');
   Route::post('/register', [AuthController::class, 'register_post'])->middleware('guest');

   Route::name('login')->get('/', [AuthController::class, 'login'])->middleware('guest');
   Route::post('/', [AuthController::class, 'login_post'])->middleware('guest');

   Route::name('logout')->get('/logout', [AuthController::class, 'logout'])->middleware('auth');

   Route::name('cabinet')->get('/cabinet', [AuthController::class, 'cabinet'])->middleware('auth');
});
