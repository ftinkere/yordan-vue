<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\ArticleTag
 *
 * @property int $id
 * @property int $article_id
 * @property string $tag
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag query()
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereTag($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ArticleTag whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ArticleTag extends Model
{
    use HasFactory;

    protected $table = 'article_tags';
    protected $fillable = ['article_id', 'tag'];

    public function article() {
        return $this->belongsTo(Article::class, 'article_id', 'id');
    }
}
