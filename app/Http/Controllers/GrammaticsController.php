<?php

namespace App\Http\Controllers;

use App\Models\Grammatic;
use App\Models\GrammaticValue;
use App\Models\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class GrammaticsController extends Controller
{
    public function index(Request $request, $code) {
        $language = Language::with([
            'grammatics',
            'grammatics.grammatic_values',
            'base_articles',
        ])->findOrFail($code);

        $editMode = $request->has('editMode');

        return Inertia::render('LanguageGrammatics', compact('language', 'editMode'));
    }

    public function store(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'name' => 'required',
            'article' => 'nullable',
        ], messages: [
            'name.required' => 'Обязательно указать название грамматической категории.'
        ]);

        Grammatic::create([
            ...$data,
            'language_id' => $language->id,
        ]);

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.grammatics', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function update(Request $request, $code, $grammatic_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $grammatic = Grammatic::findOrFail($grammatic_id);
        Gate::authorize('grammatic-is-language', $grammatic);

        $data = $request->validate([
            'name' => 'required',
            'article' => 'nullable',
        ], messages: [
            'name.required' => 'Обязательно указать название грамматической категории.'
        ]);

        $grammatic->update($data);

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.grammatics', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function delete(Request $request, $code, $grammatic_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $grammatic = Grammatic::findOrFail($grammatic_id);
        Gate::authorize('grammatic-is-language', $grammatic);

        $grammatic->delete();

        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.grammatics', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function value_store(Request $request, $code, $grammatic_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $grammatic = Grammatic::findOrFail($grammatic_id);
        Gate::authorize('grammatic-is-language', $grammatic);

        $data = $request->validate([
            'value' => 'required',
            'short' => 'required',
            'article' => 'nullable',
        ], messages: [
            'value.required' => 'Обязательно нужно указать название грамматического значения.',
            'short.required' => 'Обязательно нужно указать короткое обозначение значения.',
        ]);

        $value = GrammaticValue::create([
            ...$data,
            'grammatic_id' => $grammatic->id,
        ]);

        $grammatic->updateTimestamps();
        $grammatic->save();
        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.grammatics', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function value_update(Request $request, $code, $grammatic_id, $value_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $grammatic = Grammatic::findOrFail($grammatic_id);
        Gate::authorize('grammatic-is-language', $grammatic);
        $value = GrammaticValue::findOrFail($value_id);
        Gate::authorize('grammatic_value-is-language', $value);
        Gate::authorize('grammatic_value-is-grammatic', [$value, $grammatic]);

        $data = $request->validate([
            'value' => 'required',
            'short' => 'required',
            'article' => 'nullable',
        ], messages: [
            'value.required' => 'Обязательно указать название грамматического значения.',
            'short.required' => 'Обязательно указать короткое обозначение значения.',
        ]);

        $value->update($data);

        $grammatic->updateTimestamps();
        $grammatic->save();
        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.grammatics', ['code' => $language->id, 'editMode' => $editMode]);
    }

    public function value_delete(Request $request, $code, $grammatic_id, $value_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $grammatic = Grammatic::findOrFail($grammatic_id);
        Gate::authorize('grammatic-is-language', $grammatic);
        $value = GrammaticValue::findOrFail($value_id);
        Gate::authorize('grammatic_value-is-language', $value);
        Gate::authorize('grammatic_value-is-grammatic', [$value, $grammatic]);

        $value->delete();

        $grammatic->updateTimestamps();
        $grammatic->save();
        $language->updateTimestamps();
        $language->save();

        $editMode = $request->has('editMode');

        return redirect()->route('languages.grammatics', ['code' => $language->id, 'editMode' => $editMode]);
    }
}
