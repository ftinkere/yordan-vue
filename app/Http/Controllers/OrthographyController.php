<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Orthographeme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class OrthographyController extends Controller
{
    public function index(Request $request, $code) {
        $language = Language::with('orthographemes')->findOrFail($code);

        return Inertia::render('LanguageOrthography', compact('language'));
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

        return redirect()->route('languages.orthography', ['code' => $language->id]);
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

        return redirect()->route('languages.orthography', ['code' => $language->id]);
    }

    public function delete(Request $request, $code, $orthographeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $orthographeme = Orthographeme::findOrFail($orthographeme_id);
        Gate::authorize('orthographeme-is-language', $orthographeme);

        $orthographeme->delete();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.orthography', ['code' => $language->id]);
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

        return redirect()->route('languages.orthography', ['code' => $language->id]);
    }
}
