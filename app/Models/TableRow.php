<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\TableRow
 *
 * @property int $id
 * @property int $table_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read  \Illuminate\Database\Eloquent\Collection<int, TableCell> $cells
 * @property-read  Table $table
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow query()
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow whereTableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TableRow whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class TableRow extends Model
{
    use HasFactory;

    protected $table = 'table_rows';
    protected $fillable = ['table_id'];

    function table() {
        return $this->belongsTo(Table::class);
    }

    function cells() {
        return $this->hasMany(TableCell::class, 'row_id');
    }
}
