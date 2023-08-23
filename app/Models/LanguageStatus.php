<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LanguageStatus
 *
 * @property int $id
 * @property int $language_id
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus query()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageStatus whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LanguageStatus extends Model
{
    use HasFactory;

    protected $table = 'language_statuses';

    protected $fillable = ['language_id', 'status'];

    public function language() {
        return $this->belongsTo(Language::class, 'language_id', 'id');
    }
}
