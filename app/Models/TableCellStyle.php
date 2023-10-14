<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TableCellStyle
 *
 * @property int $id
 * @property int $cell_id
 * @property string $style
 * @property string $value
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read TableCell $cell
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle query()
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle whereCellId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle whereStyle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCellStyle whereValue($value)
 * @mixin \Eloquent
 */
class TableCellStyle extends Model
{
    use HasFactory;

    protected $table = 'table_cell_styles';
    protected $fillable = ['cell_id', 'style', 'value'];
    protected $touches = ['cell'];

    function cell() {
        return $this->belongsTo(TableCell::class, 'cell_id');
    }
}
