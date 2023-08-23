<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MainController;
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

   Route::name('logout')->get('/logout', [AuthController::class, 'logout'])->middleware('auth');

   Route::name('cabinet')->get('/cabinet', [AuthController::class, 'cabinet'])->middleware('auth');

   Route::name('verify')->get('/verify', [AuthController::class, 'verify']);
});
