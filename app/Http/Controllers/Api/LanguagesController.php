<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Language;
use Illuminate\Http\Request;

class LanguagesController extends Controller {
    public function lastLanguages() {
        return Language::where('hidden', false)
            ->orderBy('updated_at', 'desc')
            ->limit(15)
            ->get();
    }

    public function ownedLanguages(Request $request) {
        return Language::where('user_id', $request->user()?->id)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
