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

Route::prefix('auth')->middleware('guest')->group(static function() {
   Route::name('register')->get('/register', [AuthController::class, 'register']);
   Route::post('/register', [AuthController::class, 'register_post']);

   Route::name('login')->get('/', [AuthController::class, 'login']);
   Route::post('/', [AuthController::class, 'login_post']);
});
