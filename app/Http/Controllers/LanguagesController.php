<?php

namespace App\Http\Controllers;

use App\Http\Requests\LanguageStoreRequest;
use App\Models\Language;
use Illuminate\Http\Request;
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

        return ['message' => 'ok'];
    }

    /**
     * Display the specified resource.
     */
    public function view($id)
    {
        $language = Language::with(['base_articles', 'dev_note', 'statuses'])->findOrFail($id);
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
