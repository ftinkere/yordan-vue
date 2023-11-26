<?php

namespace App\Models;

use App\Models\HasLanguage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Table
 *
 * @property int $id
 * @property int $language_id
 * @property string $name
 * @property string|null $caption
 * @property int $is_zebra
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read  \Illuminate\Database\Eloquent\Collection<int, TableRow> $rows
 * @property-read  \Illuminate\Database\Eloquent\Collection<int, TableCell> $cells
 * @property-read  Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|Table newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Table newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Table query()
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereCaption($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereIsZebra($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Table whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Table extends Model implements HasLanguage
{
    use HasFactory;

    protected $table = 'tables';
    protected $fillable = ['language_id', 'name', 'caption', 'is_zebra', 'order'];
    protected $touches = ['language'];

    static function newOrder(Language $language) {
        return $language->tables()->max('order') + 1;
    }

    function language() {
        return $this->belongsTo(Language::class);
    }

    function rows() {
        return $this->hasMany(TableRow::class)->orderBy('order')->orderBy('id');
    }

    function cells() {
        return $this->hasManyThrough(TableCell::class, TableRow::class, 'table_id', 'row_id')
            ->orderBy('order')
            ->orderBy('id');
    }
}
