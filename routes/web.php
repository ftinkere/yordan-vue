<?php

use App\Http\Controllers\ArticlesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GrammaticsController;
use App\Http\Controllers\LanguagesController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\OrthographyController;
use App\Http\Controllers\PhoneticsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TablesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VocabularyController;
use Illuminate\Support\Facades\Route;
use Tightenco\Ziggy\Ziggy;

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
Route::name('info')->get('/info', [MainController::class, 'info']);
Route::name('about')->get('/about', [MainController::class, 'about']);

Route::name('ziggy')->get('/ziggy', static fn () => response()->json(new Ziggy));

Route::name('profile')->get('/profile/{user}', [UserController::class, 'profile']);
Route::name('user.')->middleware('auth')->group(static function () {
    Route::name('cabinet')->get('/cabinet', [UserController::class, 'cabinet']);

    Route::name('update')->post('/user', [UserController::class, 'update']);
    Route::name('push_avatar')->post('/user/avatar', [UserController::class, 'push_avatar']);
    Route::name('change_password')->post('/user_password', [UserController::class, 'change_password']);
    Route::name('resend_confirmation')->post('/user/resend_confirmation', [UserController::class, 'resend_confirmation']);
});

Route::prefix('auth')->group(static function() {
   Route::name('register')->get('/register', [AuthController::class, 'register'])->middleware('guest');
   Route::post('/register', [AuthController::class, 'register_post'])->middleware('guest');

   Route::name('login')->get('/', [AuthController::class, 'login'])->middleware('guest');
   Route::post('/', [AuthController::class, 'login_post'])->middleware('guest');

   Route::name('logout')->get('/logout', [AuthController::class, 'logout'])->middleware('auth');

   Route::name('verify')->get('/verify', [AuthController::class, 'verify']);
});

Route::name('languages')->get('/languages', [LanguagesController::class, 'index']);
Route::name('languages.')->prefix('/languages')->group(static function() {
    Route::name('view')->get('/{code}', [LanguagesController::class, 'view']);
    Route::name('store')->post('', [LanguagesController::class, 'store']);
    Route::name('update')->post('/{code}', [LanguagesController::class, 'update']);
    Route::name('delete')->delete('/{code}', [LanguagesController::class, 'destroy']);
    Route::name('restore')->get('/{code}/restore', [LanguagesController::class, 'restore']);
    Route::name('about')->post('/{code}/about', [LanguagesController::class, 'updateAbout']);
    Route::name('flag')->post('/{code}/flag', [LanguagesController::class, 'pushFlag']);
    Route::name('image')->post('/{code}/image', [LanguagesController::class, 'pushImage']);
    Route::name('action')->get('/{code}/action', [LanguagesController::class, 'action']);
    Route::name('hide')->post('/{code}/hide', [LanguagesController::class, 'hiddenChange']);

    Route::name('settings')->get('/{code}/settings', [LanguagesController::class, 'settings']);

    Route::name('articles')->get('/{code}/articles', [ArticlesController::class, 'index']);
    Route::name('articles.')->prefix('/{code}/articles')->group(static function() {
        Route::name('view')->get('/{article}', [ArticlesController::class, 'view']);
        Route::name('store')->post('', [ArticlesController::class, 'store']);
        Route::name('update')->post('/{article}', [ArticlesController::class, 'update']);
        Route::name('tags')->post('/{article}/tags', [ArticlesController::class, 'pushTag']);
        Route::name('tags.delete')->delete('/{article}/tags/{tag}', [ArticlesController::class, 'deleteTag']);
        Route::name('publish')->post('/{article}/publish', [ArticlesController::class, 'publish']);
        Route::name('unpublish')->post('/{article}/unpublish', [ArticlesController::class, 'unpublish']);
        Route::name('delete')->delete('/{article}', [ArticlesController::class, 'delete']);
    });

    Route::name('tables')->get('/{code}/tables', [TablesController::class, 'index']);
    Route::name('tables.')->prefix('/{code}/tables')->group(static function() {
        Route::name('store')->post('', [TablesController::class, 'store']);
        Route::name('import')->post('/import', [TablesController::class, 'import']);
        Route::name('update')->post('/{table}', [TablesController::class, 'update']);
        Route::name('shadow')->post('/{table}/shadow', [TablesController::class, 'shadow']);
        Route::name('delete')->delete('/{table}', [TablesController::class, 'delete']);
        Route::name('move')->post('/{table}/move', [TablesController::class, 'move']);
        Route::name('rows.store')->post('/{table}/rows', [TablesController::class, 'add_row']);
        Route::name('rows.delete')->delete('/{table}/rows/{row}', [TablesController::class, 'delete_row']);
        Route::name('columns.store')->post('/{table}/columns', [TablesController::class, 'add_column']);
        Route::name('columns.delete')->delete('/{table}/columns/{column}', [TablesController::class, 'delete_col']);
        Route::name('cells.store')->post('/{table}/rows/{row}/cells', [TablesController::class, 'add_cell']);
        Route::name('cells.update')->post('/{table}/cells/{cell}', [TablesController::class, 'update_cell'])->whereNumber('cell');
        Route::name('cells.update_content')->post('/{table}/cells/{cell}/content', [TablesController::class, 'update_cell_content'])->whereNumber('cell');
        Route::name('cells.merge')->post('/{table}/cells/merge', [TablesController::class, 'update_merge']);
        Route::name('cells.unmerge')->post('/{table}/cells/{cell}/unmerge', [TablesController::class, 'update_unmerge'])->whereNumber('cell');
        Route::name('cells.delete')->delete('/{table}/cells/{cell}', [TablesController::class, 'delete_cell'])->whereNumber('cell');
        Route::name('cells.styles.update')->post('/{table}/cells/styles/update', [TablesController::class, 'update_style']);
        Route::name('cells.styles.toggle')->post('/{table}/cells/styles/toggle', [TablesController::class, 'toggle_style']);
        Route::name('cells.styles.border')->post('/{table}/cells/styles/border', [TablesController::class, 'borderChange']);
    });

    Route::name('vocabulary')->get('/{code}/vocabulary', [VocabularyController::class, 'index']);
    Route::name('vocabulary.')->prefix('/{code}/vocabulary')->group(static function () {
        Route::name('view')->get('/{vocabula}', [VocabularyController::class, 'view'])->whereNumber('vocabula');
        Route::name('store')->post('', [VocabularyController::class, 'store']);
        Route::name('update')->post('/{vocabula}', [VocabularyController::class, 'update'])->whereNumber('vocabula');
        Route::name('delete')->delete('/{vocabula}', [VocabularyController::class, 'delete'])->whereNumber('vocabula');
        Route::name('image')->post('/{vocabula}/image', [VocabularyController::class, 'pushImage'])->whereNumber('vocabula');
        Route::name('image')->delete('/{vocabula}/image', [VocabularyController::class, 'deleteImage'])->whereNumber('vocabula');

        Route::name('lexemes.search')->get('/search', [VocabularyController::class, 'searchLexemes']);
        Route::name('lexemes.')->prefix('/{vocabula}/lexemes')->group(static function () {
            Route::name('store')->post('', [VocabularyController::class, 'lexemeStore']);
            Route::name('update')->post('/{lexeme}', [VocabularyController::class, 'lexemeUpdate']);
            Route::name('delete')->delete('/{lexeme}', [VocabularyController::class, 'lexemeDelete']);

            Route::name('links.')->prefix('/{lexeme}/links')->group(static function () {
                Route::name('store')->post('', [VocabularyController::class, 'linkStore']);
                Route::name('delete')->delete('/{link}', [VocabularyController::class, 'linkDelete']);
            });
        })->whereNumber('vocabula');
    });

    Route::name('phonetic')->get('/{code}/phonetic', [PhoneticsController::class, 'index']);
    Route::name('phonetic.')->prefix('/{code}/phonetic')->group(static function () {
        Route::name('article')->post('/article', [PhoneticsController::class, 'article']);
        Route::name('edit')->post('/{language_sound}/edit', [PhoneticsController::class, 'edit']);
        Route::name('toggle-sound')->post('/toggle-sound', [PhoneticsController::class, 'toggleSoundNew']);
        Route::name('sounds.store')->post('/sounds', [PhoneticsController::class, 'addSound']);
        Route::name('sounds.delete')->delete('/sounds/{sound}', [PhoneticsController::class, 'deleteSound']);
    });

    Route::name('orthography')->get('/{code}/orthography', [OrthographyController::class, 'index']);
    Route::name('orthography.')->prefix('/{code}/orthography')->group(static function () {
        Route::name('order')->post('/order', [OrthographyController::class, 'order']);
        Route::name('store')->post('', [OrthographyController::class, 'store']);
        Route::name('update')->post('/{orthographeme}', [OrthographyController::class, 'update']);
        Route::name('delete')->delete('/{orthographeme}', [OrthographyController::class, 'delete']);

        Route::name('pronunciations.')->prefix('/{orthographeme}/pronunciations')->group(static function () {
            Route::name('store')->post('', [OrthographyController::class, 'pronunciations_store']);
            Route::name('update')->post('/{pronunciation}', [OrthographyController::class, 'pronunciations_update']);
            Route::name('delete')->delete('/{pronunciation}', [OrthographyController::class, 'pronunciations_delete']);
        });
    });

    Route::name('grammatics')->get('/{code}/grammatics', [GrammaticsController::class, 'index']);
    Route::name('grammatics.')->prefix('/{code}/grammatics')->group(static function () {
        Route::name('store')->post('', [GrammaticsController::class, 'store']);
        Route::name('update')->post('/{grammatics}', [GrammaticsController::class, 'update']);
        Route::name('delete')->delete('/{grammatics}', [GrammaticsController::class, 'delete']);

        Route::name('values.')->prefix('/{grammatics}/values')->group(static function () {
            Route::name('store')->post('', [GrammaticsController::class, 'value_store']);
            Route::name('update')->post('/{value}', [GrammaticsController::class, 'value_update']);
            Route::name('delete')->delete('/{value}', [GrammaticsController::class, 'value_delete']);
        });
    });

});
