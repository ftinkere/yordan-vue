<?php

namespace App\Http\Controllers;

use App\Models\Language;
use Illuminate\Http\Request;
use Inertia\Inertia;

class VocabularyController extends Controller
{
    public function index(Request $request, $code) {
        $language = Language::findOrFail($code);
        $language->can_edit = $request->user()->can('edit-language', $language);

        return Inertia::render('LanguageVocabulary', compact('language'));
    }
}
