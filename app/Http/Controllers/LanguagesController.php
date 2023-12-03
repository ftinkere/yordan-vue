<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageStoreRequest;
use App\Http\Requests\LanguageUpdateRequest;
use App\Models\BaseArticles;
use App\Models\Language;
use App\Models\LanguageStatus;
use App\Services\BaseArticlesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
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
    public function store(Request $request)
    {
        ['name' => $name] = $request->validate([
            'name' => 'required|filled',
        ], $messages = [
            'name.required' => 'Укажите название языка',
            'name.filled' => 'Укажите название языка',
        ]);

        $language = Language::create([
            'name' => $name,
            'user_id' => Auth::id(),
            'status' => 'Новый',
        ]);
        $language->save();

        return redirect()->route('languages.view', ['code' => $language->id]);
    }

    /**
     * Display the specified resource.
     */
    public function view($code)
    {
        /** @var Language $language */
        $language = Language::with([
            'base_articles',
            'user',
        ])->findOrFail($code);

        return Inertia::render('LanguageView', [
            ...compact('language'),
            'action' => $language->get_action(),
            ]);
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
    public function update(Request $request, string $code)
    {
        $language = Language::find($code);

        $data = $request->validate([
            'name' => 'required|filled',
            'autonym' => '',
            'autonym_transcription' => '',
            'type' => '',
            'status' => '',
        ], messages: [
            'name.required' => 'Имя не может быть пустым.',
            'name.filled' => 'Имя не может быть пустым.',
        ]);

        $language->update($data);

        $language->status = $request->get('status');
        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.view', ['code' => $language->id]);
    }

    public function updateAbout(Request $request, $code, BaseArticlesService $articlesService) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'about' => '',
        ]);

        $articlesService->update($language, $data);

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.view', ['code' => $language->id]);
    }

    public function pushFlag(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $request->validate([
            'flag' => 'required|file|image'
        ], messages: [
            'flag.required' => 'Изображение обязательно.',
            'flag.file' => 'Флаг должен быть файлом.',
            'flag.image' => 'Флаг должен быть изображением.',
        ]);
        $file = $request->file('flag');

        if ($file !== null) {
            $path = $file->storeAs('/flags', (string)$language->id . '.' . $file->getClientOriginalExtension(), 'public');
            $path = '/storage/' . $path;
            $language->flag = $path;
            $language->updateTimestamps();
            $language->save();
//            return ['message' => 'Success', 'path' => $path];
            return redirect()->route('languages.view', ['code' => $language->id]);
        }

        Session::flash('message', ['type' => 'error', 'message' => 'Ошибка загрузки файла.']);
        return redirect()->route('languages.view', ['code' => $language->id])->withErrors('Ошибка загрузки файла.');
//        return ['message' => 'Error'];
    }

    public function pushImage(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $request->validate([
            'image' => 'required|file|image'
        ], messages: [
            'image.required' => 'Изображение обязательно.',
            'image.file' => 'Изображение должно быть файлом.',
            'image.image' => 'Файл должен быть изображением.',
        ]);
        $file = $request->file('image');

        if ($file !== null) {
            $path = $file->storeAs('/images/' . $language->id, $file->getClientOriginalName(), 'public');
            $path = '/storage/' . $path;
            return ['message' => 'Success', 'path' => $path];
//            return redirect()->route('languages.view', ['code' => $language->id]);
        }
        Session::flash('message', ['type' => 'error', 'message' => 'Ошибка загрузки файла.']);
        return redirect()->route('languages.view', ['code' => $language->id])->withErrors('Ошибка загрузки файла.');
//        return ['message' => 'Error'];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $language->delete();

        return redirect()->route('main');
    }

    public function restore(string $code) {
        $language = Language::withTrashed()->findOrFail($code);
        Gate::authorize('edit-language', $language);

        $e = $language->restore();

        return redirect()->route('languages.view', $language->id);
    }

    public function action($code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        return redirect()->route('languages.view', ['code' => $language->id]);
    }

    public function settings($code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        return Inertia::render('LanguageSettings', compact('language'));
    }
}
