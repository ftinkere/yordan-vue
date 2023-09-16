<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\BaseArticles
 *
 * @property int $id
 * @property int $language_id
 * @property string|null $about
 * @property string|null $phonetic
 * @property string|null $grammatic
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles query()
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles whereAbout($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles whereGrammatic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles wherePhonetic($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BaseArticles whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class BaseArticles extends Model
{
    use HasFactory;

    protected $table = 'base_articles';
    protected $fillable = ['language_id', 'about', 'phonetic', 'grammatic'];

    public function language() {
        return $this->belongsTo(Language::class);
    }

}
