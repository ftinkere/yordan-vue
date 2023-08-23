<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\GrammaticValue
 *
 * @property int $id
 * @property int $grammatic_id
 * @property string $value
 * @property string|null $article
 * @property string $short
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Grammatic $grammatic
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue query()
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereGrammaticId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereShort($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|GrammaticValue whereValue($value)
 * @mixin \Eloquent
 */
class GrammaticValue extends Model
{
    use HasFactory;

    protected $table = 'grammatic_values';

    protected $fillable = ['grammatic_id', 'value', 'short', 'article'];

    public function grammatic() {
        return $this->belongsTo(Grammatic::class);
    }
}
