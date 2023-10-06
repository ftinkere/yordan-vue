<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\OrthographemePronunciation
 *
 * @property int $id
 * @property int $orthographeme_id
 * @property int $pronunciation
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Orthographeme $orthographeme
 * @property-read \App\Models\LanguageSound $language_sound
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\PronunciationContext> $contexts
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation query()
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation whereOrthographemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation wherePronunciation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation whereUpdatedAt($value)
 * @property-read int|null $contexts_count
 * @property string|null $context
 * @method static \Illuminate\Database\Eloquent\Builder|OrthographemePronunciation whereContext($value)
 * @mixin \Eloquent
 */
class OrthographemePronunciation extends Model
{
    use HasFactory;

    protected $table = 'orthographeme_pronunciations';
    protected $fillable = ['orthographeme_id', 'pronunciation', 'context'];

    public function orthographeme() {
        return $this->belongsTo(Orthographeme::class, 'orthographeme_id', 'id');
    }

    public function language_sound() {
        return $this->belongsTo(LanguageSound::class, 'pronunciation', 'id');
    }

    public function contexts() {
        return $this->hasMany(PronunciationContext::class, 'pronunciation_id', 'id');
    }
}

