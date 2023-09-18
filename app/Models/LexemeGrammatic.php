<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LexemeGrammatic
 *
 * @property int $id
 * @property int $lexeme_id
 * @property int $grammatic_value_id
 * @property int $is_variable
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\GrammaticValue $grammatic_value
 * @property-read \App\Models\Lexeme $lexeme
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic query()
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic whereGrammaticValueId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic whereIsVariable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic whereLexemeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LexemeGrammatic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LexemeGrammatic extends Model
{
    use HasFactory;

    protected $table = 'lexeme_grammatics';
    protected $fillable = ['lexeme_id', 'grammatic_value_id', 'is_variable'];
    protected $appends = ['short'];

    public function short(): Attribute {
        return Attribute::make(function () {
            return $this->grammatic_value->short;
        });
    }

    public function lexeme() {
        return $this->belongsTo(Lexeme::class, 'lexeme_id', 'id');
    }

    public function grammatic_value() {
        return $this->belongsTo(GrammaticValue::class, 'grammatic_value_id', 'id');
    }

    public static function is_has_grammatic($lexeme_id, $value_id) {
        return self::where('lexeme_id', '=', $lexeme_id)
            ->where('grammatic_value_id', '=', $value_id)
            ->count() > 0;
    }

    public static function lexeme_grammatic_by_data($lexeme_id, $value_id) {
        return self::where('lexeme_id', '=', $lexeme_id)
            ->where('grammatic_value_id', '=', $value_id)
            ->first();
    }
}
