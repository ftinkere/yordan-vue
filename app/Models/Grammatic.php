<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Grammatic
 *
 * @property int $id
 * @property string $name
 * @property int $language_id
 * @property string|null $article
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\GrammaticValue> $grammatic_values
 * @property-read int|null $grammatic_values_count
 * @property-read \App\Models\Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic query()
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic whereArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Grammatic whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Grammatic extends Model
{
    use HasFactory;

    protected $table = 'grammatics';

    protected $fillable = ['name', 'language_id', 'article', 'order'];

    public function grammatic_values() {
        return $this->hasMany(GrammaticValue::class, 'grammatic_id', 'id')
            ->orderBy('order');
    }

    public function valueOrder() {
        return $this->grammatic_values()->max('order') + 1;
    }

    public function language() {
        return $this->belongsTo(Language::class);
    }
}
