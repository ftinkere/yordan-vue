<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LanguageSound
 *
 * @property int $id
 * @property int $language_id
 * @property int $sound_id
 * @property int|null $allophone_of
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read LanguageSound|null $allophone
 * @property-read \App\Models\Sound $sound
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound query()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound whereAllophoneOf($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound whereSoundId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageSound whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LanguageSound extends Model
{
    use HasFactory;

    protected $table = 'language_sounds';
    protected $fillable = ['language_id', 'sound_id', 'allophone_of'];

    public function sound() {
        return $this->belongsTo(Sound::class, 'sound_id', 'id');
    }

    public function allophone() {
        return $this->belongsTo(self::class, 'allophone_of', 'id');
    }

    public static function in_language($language_id) {
        return self::where('language_id', '=', $language_id)->pluck('sound_id');
    }

    public static function has_sound($phoneme_id, $language_id) {
        return self::where('language_id', '=', $language_id)
            ->where('sound_id', '=', $phoneme_id)
            ->first();
    }
}
