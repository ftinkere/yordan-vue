<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TableCell
 *
 * @property int $id
 * @property int $row_id
 * @property string $content
 * @property int $is_header
 * @property int $rowspan
 * @property int $colspan
 * @property int $order
 * @property int|null $merged_in
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read TableRow $tableRow
 * @property-read Table $table
 * @property-read  \Illuminate\Database\Eloquent\Collection<int, TableCellStyle> $styles
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell query()
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereColspan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereIsHeader($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereRowId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereRowspan($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableCell whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TableCell extends Model
{
    use HasFactory;

    protected $table = 'table_cells';
    protected $fillable = ['row_id', 'content', 'is_header', 'rowspan', 'colspan', 'order', 'merged_in'];
    protected $touches = ['tableRow', 'table'];

    static function newOrder(TableRow $row) {
        return $row->cells()->max('order') + 1;
    }

    function tableRow() {
        return $this->belongsTo(TableRow::class, 'row_id');
    }

    function table() {
        return $this->tableRow->belongsTo(Table::class);
    }

    function styles() {
        return $this->hasMany(TableCellStyle::class, 'cell_id');
    }

    function mergedCells(): \Illuminate\Database\Eloquent\Builder
    {
        return self::where('merged_in', $this->id);
    }
}
