<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageStoreRequest;
use App\Http\Requests\LanguageUpdateRequest;
use App\Models\BaseArticles;
use App\Models\Language;
use App\Models\LanguageStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class LanguagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('LanguagesIndex', [
            'languages' => Language::with('user')
                ->orderBy('id', 'desc')
                ->paginate(15)
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(LanguageStoreRequest $request)
    {
        ['name' => $name] = $request->validated();
        $language = Language::create([
            'name' => $name,
            'user_id' => Auth::id(),
        ]);
        $language->save();

        $status = LanguageStatus::create([
            'language_id' => $language->id,
            'status' => 'Новый',
            ]);
        $status->save();

        return redirect()->route('languages.view', ['code' => $language->id]);
    }

    /**
     * Display the specified resource.
     */
    public function view($code)
    {
        /** @var Language $language */
        $language = Language::with(['base_articles', 'status', 'user'])->findOrFail($code);
        return Inertia::render('LanguageView', [
                ...compact('language'),
                'can_edit' => \Auth::user()->can('edit-language', $language),
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(LanguageUpdateRequest $request, string $code)
    {
        $language = Language::find($code);

        $language->update($request->validated());

        return $language->toJson();
    }

    public function updateAbout(Request $request, string $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'about' => 'required',
        ]);

        if ($language->base_articles === null) {
            $articles = new BaseArticles([
               'language_id' => $language->id,
            ]);
            $articles->save();
        } else {
            $articles = $language->base_articles;
        }

        $articles->update($data);

        return ['about' => $articles->about];
    }

    public function pushFlag(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $request->validate([
            'flag' => 'required|file|image'
        ], $messages = [
            'flag.required' => 'Изображение обязательно.',
            'flag.file' => 'Флаг должен быть файлом.',
            'flag.image' => 'Флаг должен быть изображением.',
        ]);
        $file = $request->file('flag');

        if ($file != null) {
            $path = $file->storeAs('/flags', (string)$language->id . '.' . $file->getClientOriginalExtension(), 'public');
            $path = '/storage/' . $path;
            $language->flag = $path;
            $language->save();
            return ['message' => 'Success', 'path' => $path];
        }
        return ['message' => 'Error'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function action($code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        return $language->get_action();
    }
}
