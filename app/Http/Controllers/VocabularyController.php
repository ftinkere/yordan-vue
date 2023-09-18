<?php

namespace App\Http\Controllers;

use App\Models\GrammaticValue;
use App\Models\Language;
use App\Models\Lexeme;
use App\Models\LexemeGrammatic;
use App\Models\Vocabula;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;

class VocabularyController extends Controller
{
    public function index(Request $request, $code) {
        $language = Language::with('grammatics', 'grammatics.grammatic_values')->findOrFail($code);

        $vocables = $language->vocables()
            ->with(['lexemes', 'lexemes.links', 'lexemes.links.to', 'lexemes.links.to.grammatics', 'lexemes.links.to.grammatics.grammatic_value', 'lexemes.links.to.vocabula', 'lexemes.grammatics', 'lexemes.grammatics.grammatic_value'])
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
        $language = Language::findOrFail($code);

        $vocabula = Vocabula::with('lexemes')->findOrFail($vocabula);

        return Inertia::render('VocabularyView', compact('language', 'vocabula'));
    }

    public function store(Request $request, $code) {
        $language = Language::findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'vocabula' => 'required',
            'transcription' => 'required',
            'grammatics' => 'nullable|array',
            'grammatics_variables' => 'nullable|array',
            'grammatics_general' => 'nullable|boolean',
            'lexeme' => 'nullable|array',
            'lexeme.short' => 'nullable',
            'lexeme.article' => 'nullable',
        ], messages: [
            'vocabula.required' => 'Вокабула не может быть пустой.',
            'transcription.required' => 'Транскрипция не может быть пустой.',
        ]
        );

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
        if ($data['lexeme'] && ($data['lexeme']['short'] || $data['lexeme']['article']) || $data['grammatics_general']) {
            $lexeme1 = Lexeme::create([
                'vocabula_id' => $vocabula->id,
                'short' => $data['lexeme']['short'] ?? '',
                'article' => $data['lexeme']['article'] ?? '',
            ]);
        }

        foreach ($data['grammatics'] as $gram) {
            $grammatic = GrammaticValue::findOrFail($gram);
            Gate::authorize('grammatic-value-is-language', $grammatic);

            LexemeGrammatic::create([
                'lexeme_id' => (($data['grammatics_general'] ?? true) && $lexeme1 !== null) ? $lexeme0->id : $lexeme1->id,
                'grammatic_value_id' => $grammatic->id,
                'is_variable' => is_array($data['grammatics_variables']) && in_array($gram, $data['grammatics_variables']),
            ]);
        }

        return redirect()->route('languages.vocabulary.view', ['code' => $language->id, 'vocabula' => $vocabula->id]);
    }

    public function delete(Request $request, $code, $vocabula) {
        dd(3);
    }
}
