<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\LanguageSound;
use App\Models\Sound;
use App\Services\TableService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use function Termwind\render;

class PhoneticsController extends Controller
{
    public function index(Request $request, $code, TableService $tableService) {
        $language = Language::with([
            'sounds',
            'language_sounds',
            'language_sounds.sound',
            'language_sounds.allophone',
            'language_sounds.allophone.sound',
        ])->findOrFail($code);

        $tables = $tableService->tables($language, $language->sounds, Sound::meta($language->id, is_view: true));

        return Inertia::render('LanguagePhonetic', compact('language', 'tables'));
    }
}
