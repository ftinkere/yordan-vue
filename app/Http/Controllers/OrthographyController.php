<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Orthographeme;
use App\Models\OrthographemePronunciation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class OrthographyController extends Controller
{
    public function index(Request $request, $code) {
        $language = Language::with([
            'orthographemes',
            'orthographemes.pronunciations',
            'orthographemes.pronunciations.contexts',
            'orthographemes.pronunciations.language_sound',
            'orthographemes.pronunciations.language_sound.sound',
            'orthographemes.pronunciations.language_sound.allophone',
            'orthographemes.pronunciations.language_sound.allophone.sound',
            'language_sounds',
            'language_sounds.sound',
            'language_sounds.allophone',
            'language_sounds.allophone.sound'
        ])->findOrFail($code);

        $editMode = $request->has('editMode');

        return Inertia::render('LanguageOrthography', compact('language', 'editMode'));
    }

    public function store(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'uppercase' => 'nullable',
            'lowercase' => 'required',
            'is_alphabet' => 'nullable|boolean',
        ], messages: [
            'lowercase.required' => 'Обязательно указать хотя бы малую форму орфографемы.'
        ]);

        $data['is_alphabet'] = $data['is_alphabet'] ? 1 : 0;

        Orthographeme::create([
            ...$data,
            'language_id' => $language->id,
            'order' => Orthographeme::new_order($language),
        ]);

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function update(Request $request, $code, $orthographeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $orthographeme = Orthographeme::findOrFail($orthographeme_id);
        Gate::authorize('orthographeme-is-language', $orthographeme);


        $data = $request->validate([
            'uppercase' => 'nullable',
            'lowercase' => 'required',
            'is_alphabet' => 'nullable|boolean',
        ], messages: [
            'lowercase.required' => 'Обязательно указать хотя бы малую форму орфографемы.'
        ]);

        $data['is_alphabet'] = $data['is_alphabet'] ? 1 : 0;

        $orthographeme->update($data);

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function delete(Request $request, $code, $orthographeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $orthographeme = Orthographeme::findOrFail($orthographeme_id);
        Gate::authorize('orthographeme-is-language', $orthographeme);

        $orthographeme->delete();

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function order(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        ['order' => $order] = $request->validate([
           'order' => 'array',
           'order.*' => 'integer',
        ]);

        $i = 1;
        foreach ($order as $id) {
            $orthographeme = Orthographeme::find($id);
            $orthographeme->order = $i;
            $orthographeme->save();
            $i = $i + 1;
        }

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }

    // Pronunciations


    public function pronunciations_store(Request $request, $code, $orthographeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $orthographeme = Orthographeme::findOrFail($orthographeme_id);
        Gate::authorize('orthographeme-is-language', $orthographeme);

        $data = $request->validate([
            'pronunciation' => 'required',
            'context' => 'nullable'
        ], messages: [
            'pronunciation.required' => 'Обязательно указать произношение.'
        ]);

        OrthographemePronunciation::create([
            ...$data,
            'orthographeme_id' => $orthographeme->id,
        ]);

        $orthographeme->updateTimestamps();
        $orthographeme->save();
        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function pronunciations_update(Request $request, $code, $orthographeme_id, $pronunciation_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $orthographeme = Orthographeme::findOrFail($orthographeme_id);
        Gate::authorize('orthographeme-is-language', $orthographeme);
        $pronunciation = OrthographemePronunciation::findOrFail($pronunciation_id);
        Gate::authorize('pronunciation-is-language', $pronunciation);

        $data = $request->validate([
            'pronunciation' => 'required',
            'context' => 'nullable'
        ], messages: [
            'pronunciation.required' => 'Обязательно указать произношение.'
        ]);

        $pronunciation->update($data);

        $orthographeme->updateTimestamps();
        $orthographeme->save();
        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function pronunciations_delete(Request $request, $code, $orthographeme_id, $pronunciation_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $orthographeme = Orthographeme::findOrFail($orthographeme_id);
        Gate::authorize('orthographeme-is-language', $orthographeme);
        $pronunciation = OrthographemePronunciation::findOrFail($pronunciation_id);
        Gate::authorize('pronunciation-is-language', $pronunciation);

        $pronunciation->delete();

        $orthographeme->updateTimestamps();
        $orthographeme->save();
        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.orthography', ['code' => $language->id, 'editMode' => $editMode]);
    }
}
