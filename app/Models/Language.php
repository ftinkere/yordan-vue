<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property string $name
 * @property string|null $autonym
 * @property string|null $autonym_transcription
 * @property string|null $type
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\BaseArticles|null $base_articles
 * @property-read \App\Models\LanguageDevNote|null $dev_note
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Orthographeme> $orthographemes
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Grammatic> $grammatics
 * @property-read int|null $grammatics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Sound> $sounds
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LanguageSound> $language_sounds
 * @property-read int|null $sounds_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LanguageStatus> $statuses
 * @property-read int|null $statuses_count
 * @property-read \App\Models\User|null $user
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Vocabula> $vocables
 * @property-read int|null $vocables_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Article> $articles
 * @property-read int|null $articles_count
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
 * @mixin \Eloquent
 */
class Language extends Model
{
    use HasFactory;

    protected $table = 'languages';
    protected $fillable = ['name', 'user_id', 'autonym', 'autonym_transcription', 'type'];

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

    public function base_articles() {
        return $this->hasOne(BaseArticles::class, 'language_id', 'id');
    }

    public function statuses() {
        return $this->hasMany(LanguageStatus::class, 'language_id', 'id');
    }

    public function has_status($status) {
        return $this->hasOne(LanguageStatus::class, 'language_id', 'id')
            ->where('status', '=', $status)->first();
    }

    public function dev_note() {
        return $this->hasOne(LanguageDevNote::class, 'language_id', 'id');
    }

    public function articles() {
        return $this->hasMany(Article::class, 'language_id', 'id')
            ->orderBy(DB::raw('`published_at` IS NULL'), 'desc')
            ->orderBy('published_at', 'desc');
    }

    public function tags() {
        return ArticleTag::select('tag')
            ->whereIn('article_id', $this->articles()->pluck('id'))
            ->groupBy('tag')
            ->get();
    }

    public function orthographemes() {
        return $this->hasMany(Orthographeme::class, 'language_id', 'id')
            ->orderBy('order');
    }
}
