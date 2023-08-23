<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\Vocabula
 *
 * @property int $id
 * @property int $language_id
 * @property string $vocabula
 * @property string $transcription
 * @property string|null $image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Language $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Lexeme> $lexemes
 * @property-read int|null $lexemes_count
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula query()
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereTranscription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Vocabula whereVocabula($value)
 * @mixin \Eloquent
 */
class Vocabula extends Model
{
    use HasFactory;

    protected $table = 'vocables';
    protected $fillable = ['language_id', 'vocabula', 'data', 'transcription', 'image'];

    public function language(): BelongsTo {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function lexemes(): HasMany {
        return $this->hasMany(Lexeme::class)
            ->orderBy('group_number')->orderBy('lexeme_number');
    }
}
