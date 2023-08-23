<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Link
 *
 * @property int $id
 * @property string $type
 * @property int $from_lexeme_id
 * @property int $to_lexeme_id
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Lexeme $from
 * @property-read \App\Models\Lexeme $to
 * @method static \Illuminate\Database\Eloquent\Builder|Link newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Link query()
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereComment($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereFromLexemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereToLexemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Link whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Link extends Model
{
    use HasFactory;

    protected $table = 'links';

    protected $fillable = ['type', 'from_lexeme_id', 'to_lexeme_id', 'comment'];

    public function from() {
        return $this->belongsTo(Lexeme::class, 'from_lexeme_id');
    }

    public function to() {
        return $this->belongsTo(Lexeme::class, 'to_lexeme_id');
    }
}
