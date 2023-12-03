<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string|null $autonym
 * @property string|null $autonym_transcription
 * @property string|null $type
 * @property string $status
 * @property string|null $flag
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BaseArticles|null $base_articles
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Orthographeme> $orthographemes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grammatic> $grammatics
 * @property-read int|null $grammatics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sound> $sounds
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LanguageSound> $language_sounds
 * @property-read int|null $sounds_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vocabula> $vocables
 * @property-read int|null $vocables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Table> $tables
 * @method static \Illuminate\Database\Eloquent\Builder|Language newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Language query()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereAutonym($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereAutonymTranscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereType($value)
 * @property-read int|null $orthographemes_count
 * @property-read int|null $language_sounds_count
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereFlag($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArticleTag> $tags
 * @property-read int|null $tags_count
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereStatus($value)
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|Language onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Language whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Language withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Language withoutTrashed()
 * @mixin \Eloquent
 */
class Language extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'languages';
    protected $fillable = ['name', 'user_id', 'autonym', 'autonym_transcription', 'type'];
    protected $appends = ['can_edit', 'shows'];

    public function canEdit(): Attribute {
        return Attribute::make(
            get: function () {
                return Gate::allows('edit-language', $this);
            }
        );
    }

    public function shows(): Attribute {
        return Attribute::make(
            get: function () {
                $shows = [];
                $shows['articles'] = $this->articles()->count() > 0;
                $shows['tables'] = $this->tables()->count() > 0;
                $shows['vocabulary'] = $this->vocables()->count() > 0;
                $shows['phonetics'] = $this->language_sounds()->count() > 0 || $this->base_articles?->phonetic;
                $shows['orthography'] = $this->orthographemes()->count() > 0;
                $shows['grammatics'] = $this->grammatics()->count() > 0;

                return $shows;
            }
        );
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function vocables(): HasMany {
        return $this->hasMany(Vocabula::class, 'language_id', 'id');
    }

    public function grammatics() {
        return $this->hasMany(Grammatic::class, 'language_id', 'id');
    }

    public function sounds() {
        return $this->belongsToMany(Sound::class, 'language_sounds');
    }

    public function language_sounds() {
        return $this->hasMany(LanguageSound::class, 'language_id', 'id');
    }

    public function sounds_tables() {
        $language_sounds = $this->language_sounds()->with('sound', 'allophone')->get();


    }

    public function base_articles() {
        return $this->hasOne(BaseArticles::class, 'language_id', 'id');
    }

//    public function status() {
//        return $this->hasOne(LanguageStatus::class, 'language_id', 'id');
//    }

//    public function has_status($status) {
//        return $this->hasOne(LanguageStatus::class, 'language_id', 'id')
//            ->where('status', '=', $status)->first();
//    }

//    public function dev_note() {
//        return $this->hasOne(LanguageDevNote::class, 'language_id', 'id');
//    }

    public function articles() {
        return $this->hasMany(Article::class, 'language_id', 'id')
            ->orderBy(DB::raw('`published_at` IS NULL'), 'desc')
            ->orderBy('published_at', 'desc');
    }

    public function tags() {
        return $this->hasManyThrough(ArticleTag::class, Article::class)
            ->select('article_tags.tag')
            ->groupBy(['article_tags.tag', 'articles.language_id'])
            ->orderByRaw('MAX(`article_tags`.`id`) DESC');
//        return ArticleTag::select('tag')
//            ->whereIn('article_id', $this->articles()->pluck('id'))
//            ->groupBy('tag')
//            ->get();
    }

    public function orthographemes() {
        return $this->hasMany(Orthographeme::class, 'language_id', 'id')
            ->orderBy('order');
    }

    public function tables() {
        return $this->hasMany(Table::class)
            ->orderBy('order', 'desc');
    }

    public function get_action() {
        if (empty($this->autonym)) {
            return [
                'message' => 'У вас не указан аутоним.',
                'button' => 'Указать',
                'modal' => 'autonym',
            ];
        } else if (empty($this->autonym_transcription)) {
            return [
                'message' => 'У вас не указано произношение аутонима.',
                'button' => 'Указать',
                'modal' => 'autonym_transcription',
            ];
        } else if (empty($this->type)) {
            return [
                'message' => 'У вас не указан тип конланга.',
                'button' => 'Указать',
                'modal' => 'type',
            ];
        } else if (empty($this->base_articles?->about)) {
            return [
                'message' => 'У вас пустое описание языка. Напишите хотя бы пару предложений.',
                'button' => 'Написать',
                'modal' => 'about',
            ];
        } else if ($this->language_sounds()->count() < 2) {
            return [
                'message' => 'Добавьте в ваш язык звуки. То как ваш язык произносится.',
                'button' => 'Добавить',
                'url' => route('languages.phonetic', ['code' => $this->id, 'mode' => 'add'])
            ];
        } else if (empty($this->base_articles?->phonetic)) {
            return [
                'message' => 'Напишите статью про устройство фонетики в вашем языке.',
                'button' => 'Написать',
                'modal' => 'phonetic'
            ];
        } else if ($this->orthographemes->isEmpty()) {
            return [
                'message' => 'Добавьте в ваш язык орфографемы. То чем ваш язык записывается.',
                'button' => 'Добавить',
                'url' => route('languages.orthography', ['code' => $this->id, 'editMode' => true]),
            ];
        }  else if ($this->grammatics->isEmpty()) {
            return [
                'message' => 'Добавьте первую грамматическую категорию.',
                'button' => 'Добавить',
                'url' => route('languages.grammatics', ['code' => $this->id, 'editMode' => true]),
            ];
        } else if ($this->articles->isEmpty()) {
            return [
                'message' => 'У вас не написано ни одной статьи. Напишите хотя бы примеры вашего языка в статьях.',
                'button' => 'Написать',
                'modal' => 'article',
            ];
        }

        return null;
    }
}
