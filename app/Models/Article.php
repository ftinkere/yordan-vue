<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Article
 *
 * @property int $id
 * @property int $language_id
 * @property string $title
 * @property string $article
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Language $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ArticleTag> $tags
 * @property-read int|null $tags_c
 * @method static \Illuminate\Database\Eloquent\Builder|Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereArticle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereTags($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Article whereUpdatedAt($value)
 * @property string|null $published_at
 * @method static \Illuminate\Database\Eloquent\Builder|Article wherePublishedAt($value)ount
 * @property-read int|null $tags_count
 * @mixin \Eloquent
 */
class Article extends Model
{
    use HasFactory;
    protected $table = 'articles';
    protected $fillable = ['language_id', 'title', 'article', 'published_at'];

    protected $casts = [
        'published_at' => 'datetime:Y-m-d H:i:s'
    ];

    public function publish() {
        $this->published_at = now();
    }
    public function language() {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }

    public function tags() {
        return $this->hasMany(ArticleTag::class, 'article_id', 'id');
    }

    public function has_tag($tag) {
        return $this->hasOne(ArticleTag::class, 'article_id', 'id')
            ->where('tag', '=', $tag)
            ->first();
    }
}
