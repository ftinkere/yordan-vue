<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Orthographeme
 *
 * @property int $id
 * @property int $language_id
 * @property string $lowercase
 * @property string|null $uppercase
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme query()
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereLowercase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereUppercase($value)
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrthographemePronunciation> $pronunciations
 * @property-read int|null $pronunciations_count
 * @property int $is_alphabet
 * @method static \Illuminate\Database\Eloquent\Builder|Orthographeme whereIsAlphabet($value)
 * @mixin \Eloquent
 */
class Orthographeme extends Model
{
    use HasFactory;

    protected $table = 'orthographemes';
    protected $fillable = ['language_id', 'lowercase', 'uppercase', 'is_alphabet', 'order'];

    public function language() {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public static function new_order(Language $language) {
        return $language->orthographemes()->max('order') + 1;
    }

    public function pronunciations() {
        return $this->hasMany(OrthographemePronunciation::class, 'orthographeme_id', 'id');
    }
}
