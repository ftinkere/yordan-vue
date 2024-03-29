<?php

namespace App\Http\Controllers;

use App\Models\GrammaticValue;
use App\Models\Language;
use App\Models\Lexeme;
use App\Models\LexemeGrammatic;
use App\Models\Link;
use App\Models\Vocabula;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class VocabularyController extends Controller {
    public function index(Request $request, $code) {
        $language = Language::with('grammatics', 'grammatics.grammatic_values')->findOrFail($code);

        $vocables = $language->vocables()
            ->with([
                'lexemes',
                'lexemes.links',
                'lexemes.links.to',
                'lexemes.links.to.grammatics' => static function ($query) {
                    $query->orderBy('id');
                },
                'lexemes.links.to.grammatics.grammatic_value',
                'lexemes.links.to.vocabula',
                'lexemes.grammatics' => static function ($query) {
                    $query->orderBy('id');
                },
                'lexemes.grammatics.grammatic_value'
            ])
            ->select('vocables.*')
            ->where('language_id', '=', $code);

        if ($request->has('q') || $request->has('grammatic_filters')) {
            $vocables->leftJoin('lexemes', 'vocables.id', '=', 'lexemes.vocabula_id');
        }

        if ($request->has('grammatic_filters')) {
            $filters = $request->get('grammatic_filters') ?? [];

            foreach ($language->grammatics as $grammatic) {
                $arr = [];

                foreach ($grammatic->grammatic_values as $value) {
                    if (in_array($value->id, $filters)) {
                        $arr[] = $value->id;
                    }
                }
                if (!empty($arr)) {
                    $vocables->whereIn(
                        'vocables.id',
                        Lexeme::leftJoin('lexeme_grammatics', 'lexemes.id', '=', 'lexeme_grammatics.lexeme_id')
                            ->join('vocables', 'vocables.id', '=', 'lexemes.vocabula_id')
                            ->whereIn('lexeme_grammatics.grammatic_value_id', $arr)
                            ->distinct()
                            ->pluck('vocables.id')
                    );
                }
            }
        }

        if ($request->has('q')) {
            $q = $request->get('q');
            $vocables = $vocables
                ->where(static function (Builder $builder) use ($q) {
                    $builder->whereRaw('MATCH(lexemes.short, lexemes.article) AGAINST(?)', [$q])
                        ->orWhere('vocables.vocabula', 'LIKE', '%' . $q . '%')
                        ->orWhere('vocables.transcription', 'LIKE', '%' . $q . '%');
                })
                ->selectRaw('MAX(MATCH(lexemes.short, lexemes.article) AGAINST(?)) AS rel', [$q])
                ->orderBy('rel', 'desc');
        }

        $vocables->orderBy('vocables.vocabula')
            ->groupBy('vocables.id');
        $vocables = $vocables->paginate(15);
        $vocables->appends($request->query());

        return Inertia::render('LanguageVocabulary', compact('language', 'vocables'));
    }

    public function view(Request $request, $code, $vocabula) {
        $language = Language::with(['grammatics', 'grammatics.grammatic_values'])->findOrFail($code);

        $vocabula = Vocabula::with([
            'lexemes',
            'lexemes.links',
            'lexemes.links.to',
            'lexemes.links.to.grammatics' => static function ($query) {
                $query->orderBy('id');
            },
            'lexemes.links.to.vocabula',
            'lexemes.grammatics' => static function ($query) {
                $query->orderBy('id');
            },
            'lexemes.grammatics.grammatic_value',
        ])
            ->findOrFail($vocabula);

        return Inertia::render('VocabularyView', [
            ...compact('language', 'vocabula'),
            'editMode' => $request->has('editMode'),
        ]);
    }

    public function store(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'vocabula' => 'required',
            'transcription' => 'nullable',
            'grammatics' => 'nullable|array',
            'grammatics_variables' => 'nullable|array',
            'grammatics_general' => 'nullable|boolean',
            'lexeme' => 'nullable|array',
            'lexeme.short' => 'nullable',
            'lexeme.article' => 'nullable',
        ], messages: [
            'vocabula.required' => 'Вокабула не может быть пустой.',
        ]
        );

        DB::beginTransaction();
        try {
            $vocabula = Vocabula::create([
                ...$data,
                'language_id' => $language->id,
            ]);

            $lexeme0 = Lexeme::create([
                'vocabula_id' => $vocabula->id,
                'short' => '',
                'article' => '',
                'group_number' => 0,
                'lexeme_number' => 0,
            ]);

            $lexeme1 = null;
            if ($data['lexeme'] && ($data['lexeme']['short'] || $data['lexeme']['article'])) {
                $lexeme1 = Lexeme::create([
                    'vocabula_id' => $vocabula->id,
                    'short' => $data['lexeme']['short'] ?? '',
                    'article' => $data['lexeme']['article'] ?? '',
                ]);
            }

            foreach ($data['grammatics'] as $gram) {
                $grammatic = GrammaticValue::findOrFail($gram);
                Gate::authorize('grammatic-value-is-language', $grammatic);

                $new = LexemeGrammatic::create([
                    'lexeme_id' => (($data['grammatics_general'] ?? true) && $lexeme1 !== null) ? $lexeme0->id : ($lexeme1?->id ?? $lexeme0->id),
                    'grammatic_value_id' => $grammatic->id,
                    'is_variable' => is_array($data['grammatics_variables']) && in_array($gram, $data['grammatics_variables']),
                ]);
            }

            $language->updateTimestamps();
            $language->save();
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }
        DB::commit();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id, 'editMode' => true]);
    }

    public function update(Request $request, $code, $vocabula_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);

        $data = $request->validate([
            'vocabula' => 'required',
            'transcription' => 'nullable',
        ], messages: [
            'vocabula.required' => 'Вокабула не может быть пустой.',
        ]
        );

        $vocabula->update($data);

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function delete(Request $request, $code, $vocabula_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);

        $vocabula->delete();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary', ['code' => $language->id]);
    }

    public function pushImage(Request $request, $code, $vocabula_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);

        $request->validate([
            'image' => 'required|file|image'
        ], messages: [
            'image.required' => 'Изображение обязательно.',
            'image.file' => 'Изображение должно быть файлом.',
            'image.image' => 'Файл должен быть изображением.',
        ]);
        $file = $request->file('image');

        if ($file !== null && $file instanceof UploadedFile) {
            $path = $file->storeAs('/images/' . $language->id . '/vocables', (string)$vocabula->id . '.' . $file->getClientOriginalExtension(), 'public');
            $path = '/storage/' . $path;
            $vocabula->image = $path;
            $vocabula->save();
            return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
        }


        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        Session::flash('message', ['type' => 'error', 'message' => 'Ошибка загрузки файла.']);
        return redirect()->route('languages.view', ['code' => $language->id])->withErrors('Ошибка загрузки файла.');
    }

    public function deleteImage(Request $request, $code, $vocabula_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);

        $vocabula->image = null;
        $vocabula->save();

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function lexemeStore(Request $request, $code, $vocabula_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);

        $data = $request->validate([
            'group_number' => 'nullable|integer',
            'lexeme_number' => 'nullable|integer',
            'grammatics' => 'nullable|array',
            'grammatics_variables' => 'nullable|array',
            'short' => 'required',
            'article' => 'nullable',
            'style' => 'nullable',
        ], messages: [
            'short.required' => 'Короткое значение обязательно.'
        ]);

        $lexeme = Lexeme::create([
            'vocabula_id' => $vocabula->id,
            'short' => $data['short'],
            'article' => $data['article'] ?? '',
            'group_number' => $data['group_number'] ?? $vocabula->lexemes()->max('group_number') + 1,
            'lexeme_number' => $data['lexeme_number'] ?? $vocabula->lexemes()->where('group_number', '=', ($data['group_number'] ?? $vocabula->lexemes()->max('group_number') + 1))->max('lexeme_number') + 1,
            'style' => $data['style'] ?? null,
        ]);

        foreach ($data['grammatics'] as $gram) {
            $grammatic = GrammaticValue::find($gram);
            Gate::authorize('grammatic-value-is-language', $grammatic);

            LexemeGrammatic::create([
                'lexeme_id' => $lexeme->id,
                'grammatic_value_id' => $grammatic->id,
                'is_variable' => is_array($data['grammatics_variables']) && in_array($gram, $data['grammatics_variables']),
            ]);
        }

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function lexemeUpdate(Request $request, $code, $vocabula_id, $lexeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);
        $lexeme = Lexeme::findOrFail($lexeme_id);
        Gate::authorize('lexeme-is-language', $lexeme);

        $data = $request->validate([
            'group_number' => 'nullable|integer',
            'lexeme_number' => 'nullable|integer',
            'short' => $lexeme->group_number !== 0 || $lexeme->lexeme_number !== 0 ? 'required' : '',
            'article' => 'nullable',
            'style' => 'nullable',
        ], messages: [
            'short.required' => 'Короткое значение обязательно.'
        ]);

        $lexeme->update([
            'short' => $data['short'] ?? '',
            'article' => $data['article'] ?? '',
            'group_number' => $data['group_number'] ?? $vocabula->lexemes()->max('group_number') + 1,
            'lexeme_number' => $data['lexeme_number'] ?? $vocabula->lexemes()->where('group_number', '=', ($data['group_number'] ?? $vocabula->lexemes()->max('group_number') + 1))->max('lexeme_number') + 1,
            'style' => $data['style'] ?? null,
        ]);

        $lexeme->save();

        $lexeme->updateTimestamps();
        $lexeme->save();

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id, 'editMode' => true]);
    }

    public function lexemeUpdateGrammatics(Request $request, $code, $vocabula_id, $lexeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);
        $lexeme = Lexeme::findOrFail($lexeme_id);
        Gate::authorize('lexeme-is-language', $lexeme);

        $data = $request->validate([
            'grammatics' => 'nullable|array',
            'grammatics_variables' => 'nullable|array',
        ]);

        $grammatics = $lexeme->grammatics;
        foreach ($grammatics as $grammatic) {
            $grammatic->delete();
        }
        foreach ($data['grammatics'] as $gram) {
            $grammatic = GrammaticValue::find($gram);
            Gate::authorize('grammatic-value-is-language', $grammatic);

            LexemeGrammatic::create([
                'lexeme_id' => $lexeme->id,
                'grammatic_value_id' => $grammatic->id,
                'is_variable' => is_array($data['grammatics_variables']) && in_array($gram, $data['grammatics_variables']),
            ]);
        }

        $lexeme->save();

        $lexeme->updateTimestamps();
        $lexeme->save();

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id, 'editMode' => true]);
    }

    public function lexemeDelete(Request $request, $code, $vocabula_id, $lexeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);
        $lexeme = Lexeme::findOrFail($lexeme_id);
        Gate::authorize('lexeme-is-language', $lexeme);

        $lexeme->delete();

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function linkStore(Request $request, $code, $vocabula_id, $lexeme_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);
        $lexeme = Lexeme::findOrFail($lexeme_id);
        Gate::authorize('lexeme-is-language', $lexeme);

        $data = $request->validate([
            'type' => 'required|filled',
            'to_lexeme_id' => 'required|exists:lexemes,id',
            'comment' => 'nullable',
            'reverse' => 'nullable|array',
            'reverse.type' => 'nullable',
            'reverse.comment' => 'nullable',
        ], messages: [
            'type.required' => 'Тип связи обязателен.',
            'type.filled' => 'Тип связи обязателен.',
            'to_lexeme_id.required' => 'На что ссылаемся обязательно.',
            'to_lexeme_id.exists' => 'На что ссылаемся должно существовать.',
            'reverse.array' => 'Обратка должна быть массивом.'
        ]);

        Link::create([
            ...$data,
            'from_lexeme_id' => $lexeme->id,
        ]);

        if (isset($data['reverse']['type'])) {
            Link::create([
                ...$data['reverse'],
                'to_lexeme_id' => $lexeme->id,
                'from_lexeme_id' => $data['to_lexeme_id'],
            ]);

            $l = Lexeme::find($data['to_lexeme_id']);
            $l?->updateTimestamps();
            $l?->save();
        }

        $lexeme->updateTimestamps();
        $lexeme->save();

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function linkDelete(Request $request, $code, $vocabula_id, $lexeme_id, $link_id) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);
        $vocabula = Vocabula::findOrFail($vocabula_id);
        Gate::authorize('vocabula-is-language', $vocabula);
        $lexeme = Lexeme::findOrFail($lexeme_id);
        Gate::authorize('lexeme-is-language', $lexeme);
        $link = Link::findOrFail($link_id);
        Gate::authorize('link-is-language', $link);

        $link->delete();

        $lexeme->updateTimestamps();
        $lexeme->save();

        $vocabula->updateTimestamps();
        $vocabula->save();

        $language->updateTimestamps();
        $language->save();

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function searchLexemes(Request $request, $code) {
        $q = $request->get('q');

        $list = [];

        if (!empty($q)) {
            $sub = Vocabula::where('vocabula', 'LIKE', '%' . $q . '%');
            if (!$request->has('all_languages')) {
                $sub = $sub->where('language_id', '=', $code);
            }

            $in = $sub->pluck('id');
            $lexemes = Lexeme::whereIn('vocabula_id', $in)
                ->orderBy('group_number')
                ->orderBy('lexeme_number')
                ->limit(100)->get();
        } else {
            $lexemes = Lexeme::limit(100)->get();
        }

        foreach ($lexemes as $lexeme) {
            /**
             * @var Lexeme $lexeme
             */
            $item = []; // $lexeme->vocabula->language->name . ': ' .
            $item['view'] = $lexeme->vocabula->vocabula . ' ' . $lexeme->group_number . '.' . $lexeme->lexeme_number . ' (' . $lexeme->short . ')';
            $item['id'] = $lexeme->id;
            $list[] = $item;
        }

        if (empty($list)) {
            $item = [];
            $item['view'] = 'Введите слово в поиск';
            $item['id'] = 0;
            $list[] = $item;
        }

        return $list;
    }

    public function importPage(Request $request, $code) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);

        return Inertia::render('VocabularyImport', compact('language'));
    }

    public function importTable(Request $request, $code) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);

        ['html' => $html] = $request->validate([
            'html' => 'required'
        ]);

        libxml_use_internal_errors(true);
        $document = new DOMDocument();
        $document->loadHTML('<meta charset="utf8">' . $html);
        libxml_use_internal_errors(false);

        $xpath = new DOMXPath($document);

        DB::beginTransaction();
        try {
            /** @var \DOMElement $rows */
            $rows = $xpath->query('//table//tr');

            foreach ($rows as $row) {
                /** @var DOMElement $row */

                $dom = new DOMDocument();
                $dom->appendChild($dom->importNode($row->cloneNode(true), true));
                $cells = (new DOMXPath($dom))->query('//td|th');

                $vocabula = $cells[0]->textContent;
                $short = $cells[1]->textContent;

                $group_number = 1;
                $exists_vocabula = Vocabula::where('language_id', $language->id)
                    ->where('vocabula', $vocabula)
                    ->first();
                if ($exists_vocabula) {
                    $group_number = $exists_vocabula->lexemes()->max('group_number') + 1;
                } else {
                    $vocabula_model = Vocabula::create([
                        'language_id' => $language->id,
                        'vocabula' => $vocabula,
                        'transcription' => ' ',
                    ]);
                }

                if (isset($vocabula_model)) {
                    $zero_lexeme_model = Lexeme::create([
                        'vocabula_id' => $vocabula_model->id,
                        'short' => '',
                        'article' => '',
                        'group_number' => 0,
                        'lexeme_number' => 0,
                    ]);
                }

                $exists_lexeme = $exists_vocabula?->lexemes()->where('short', $short)->first();

                if (!$exists_lexeme) {
                    $first_lexeme_model = Lexeme::create([
                        'vocabula_id' => $vocabula_model->id,
                        'short' => $short,
                        'article' => '',
                        'group_number' => $group_number,
                        'lexeme_number' => 1,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
