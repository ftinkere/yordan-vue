<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Lexeme
 *
 * @property int $id
 * @property int $vocabula_id
 * @property int $group_number
 * @property int $lexeme_number
 * @property string $short
 * @property string $article
 * @property string|null $style
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LexemeGrammatic> $grammatics
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LexemeGrammatic> $grammatics_constant
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LexemeGrammatic> $grammatics_variable
 * @property-read int|null $grammatics_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Link> $links
 * @property-read int|null $links_count
 * @property-read \App\Models\Vocabula $vocabula
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme query()
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereGroupNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereLexemeNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Lexeme whereVocabulaId($value)
 * @mixin \Eloquent
 */
class Lexeme extends Model
{
    use HasFactory;

    protected $table = 'lexemes';
    protected $fillable = ['vocabula_id', 'meaning_id', 'group_number', 'lexeme_number', 'short', 'article', 'style', 'image', 'grammar'];

    public function vocabula(): BelongsTo {
        return $this->belongsTo(Vocabula::class, 'vocabula_id');
    }

    public function links() {
        return $this->hasMany(Link::class, 'from_lexeme_id');
    }

    public function links_by_types() {
        $links = $this->links;

        $res = [];
        foreach ($links as $link) {
            $res[$link->type][] = $link;
        }

        return $res;
    }

    public function grammatics() {
        return $this->hasMany(LexemeGrammatic::class, 'lexeme_id', 'id');
    }

    public function grammatics_constant() {
        return $this->grammatics()->where('is_variable', '=', false)->orderBy('id');
    }

    public function grammatics_variable() {
        return $this->grammatics()->where('is_variable', '=', true)->orderBy('id');
    }
}
