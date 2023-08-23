<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\PronunciationContext
 *
 * @property int $id
 * @property int $pronunciation_id
 * @property string $context
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read OrthographemePronunciation $pronunciation
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext query()
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext whereContext($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext wherePronunciationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PronunciationContext whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PronunciationContext extends Model
{
    use HasFactory;

    protected $table = 'pronunciation_contexts';
    protected $fillable = ['pronunciation_id', 'context'];

    public function pronunciation() {
        return $this->belongsTo(OrthographemePronunciation::class, 'pronunciation_id', 'id');
    }
}
