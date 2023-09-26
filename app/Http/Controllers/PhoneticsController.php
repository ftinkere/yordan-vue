<?php

namespace App\Http\Controllers;

use App\Models\BaseArticles;
use App\Models\Language;
use App\Models\LanguageSound;
use App\Models\Sound;
use App\Services\BaseArticlesService;
use App\Services\TableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use function Termwind\render;

class PhoneticsController extends Controller
{
    public function index(Request $request, $code, TableService $tableService) {
        $language = Language::with('base_articles')->findOrFail($code);
        $mode = $request->get('mode', 'view');

        if ($request->get('mode', 'view') !== 'add') {
            $sounds = Sound::view_sounds($language->id);
            $meta = Sound::meta($language->id, is_view: true);
            $tables = $tableService->tables($language, $sounds, $meta);
            $sounds = array_values($sounds->toArray());
        } else {
            $sounds = Sound::sounds($language->id);
            $meta = Sound::meta($language->id);
            $tables = $tableService->tables($language, $sounds, $meta, addMode: true);
            $sounds = array_values($sounds->toArray());
        }
        return Inertia::render('LanguagePhonetic', compact('language', 'tables', 'sounds', 'mode'));
    }

    public function article(Request $request, $code, BaseArticlesService $articlesService) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'phonetic' => '',
        ]);

        $articlesService->update($language, $data);

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.phonetic', ['code' => $language->id, 'mode' => $request->get('mode', 'edit')]);
    }

    public function edit(Request $request, $code, $language_sound_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $language_sound = LanguageSound::findOrFail($language_sound_id);
        Gate::authorize('language_sound-is-language', $language_sound);

        $data = $request->validate([
            'allophone_of' => 'nullable|exists:language_sounds,id',
        ], messages: [
            'allophone_of.exists' => 'Аллофон не существует.'
        ]);

        $language_sound->update($data);

        return redirect()->route('languages.phonetic', ['code' => $language->id, 'mode' => $request->get('mode', 'edit')]);
    }

    public function toggleSound(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        ['sound_id' => $sound_id] = $request->validate([
            'sound_id' => 'required|exists:sounds,id',
        ], messages: [
            'sound_id.required' => 'Id звука обязателен.',
            'sound_id.exists' => 'Id не существует.',
        ]);

        $sound = Sound::findOrFail($sound_id);

        $lsound = LanguageSound::where('language_id', $language->id)->where('sound_id', $sound->id)->first();
        if ($lsound) {
            $lsound->delete();
        } else {
            $lsound = LanguageSound::create([
                'language_id' => $language->id,
                'sound_id' => $sound_id,
            ]);
            $lsound->save();
        }

        return redirect()->route('languages.phonetic', ['code' => $language->id, 'mode' => $request->get('mode', 'add')]);
    }

    public function toggleSoundNew(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        ['sound_id' => $sound_id] = $request->validate([
            'sound_id' => 'required|exists:sounds,id',
        ], messages: [
            'sound_id.required' => 'Id звука обязателен.',
            'sound_id.exists' => 'Id не существует.',
        ]);

        $sound = Sound::findOrFail($sound_id);

        $lsound = LanguageSound::where('language_id', $language->id)->where('sound_id', $sound->id)->first();
        if ($lsound) {
            $lsound->delete();
        } else {
            $lsound = LanguageSound::create([
                'language_id' => $language->id,
                'sound_id' => $sound_id,
            ]);
            if ($lsound->save()) {
                return ['language_has' => true];
            }
        }
        return ['language_has' => false];
//        return redirect()->route('languages.phonetic', ['code' => $language->id, 'mode' => $request->get('mode', 'add')]);
    }

}
