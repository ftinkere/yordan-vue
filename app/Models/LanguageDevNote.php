<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\LanguageDevNote
 *
 * @property int $id
 * @property int $language_id
 * @property string $note
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote query()
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote whereNote($value)
 * @method static \Illuminate\Database\Eloquent\Builder|LanguageDevNote whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class LanguageDevNote extends Model
{
    use HasFactory;

    protected $table = 'language_dev_notes';
    protected $fillable = ['language_id', 'note'];
}
