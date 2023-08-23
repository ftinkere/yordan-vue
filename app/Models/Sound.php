<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Sound
 *
 * @property int $id
 * @property string $sound
 * @property string $table
 * @property string $row
 * @property string $column
 * @property string|null $sub_column
 * @property int|null $language_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static Builder|Sound newModelQuery()
 * @method static Builder|Sound newQuery()
 * @method static Builder|Sound query()
 * @method static Builder|Sound whereColumn($value)
 * @method static Builder|Sound whereCreatedAt($value)
 * @method static Builder|Sound whereId($value)
 * @method static Builder|Sound whereLanguageId($value)
 * @method static Builder|Sound whereRow($value)
 * @method static Builder|Sound whereSound($value)
 * @method static Builder|Sound whereSubColumn($value)
 * @method static Builder|Sound whereTable($value)
 * @method static Builder|Sound whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Sound extends Model
{
    use HasFactory;

    protected $table = 'sounds';
    protected $fillable = ['sound', 'table', 'row', 'column', 'sub_column', 'language_id'];

    public function language_sound($id) {
        return LanguageSound::where('sound_id', '=', $this->id)
            ->where('language_id', '=', $id)
            ->first();
    }

    public static function only_language_sounds($language_id) {
        return self::where('language_id', '=', $language_id)->get();
    }

    public static function sound_by_rc(string $table, string $row, string $column, string|null $sub_column = null, int|null $language_id = null) {
        return self::where('table', '=', $table)
            ->where('row', '=', $row)
            ->where('column', '=', $column)
            ->where('sub_column', '=', $sub_column)
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            })->first();
    }

    public static function sounds_row(string $table, string $row, int|null $language_id = null) {
        return self::where('table', '=', $table)
            ->where('row', '=', $row)
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            })->get();
    }

    public static function sounds_table(string $table, int|null $language_id = null) {
//        $res = [];
//        foreach (self::rows_list($table, $language_id) as $row) {
//            $res[$row] = self::phonemes_row($table, $row, $language_id);
//        }
//        return $res;
        return self::where('table', '=', $table)
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            })->get();
    }

    public static function sounds(string|null $language_id = null) {
        return self::where(static function(Builder $query) use ($language_id) {
            $query->whereNull('language_id')
                ->orWhere('language_id', '=', $language_id);
        })->get();
    }

    public static function meta(string|null $language_id = null, $is_view = false) {
        $res = [];
        foreach (self::tables_list($language_id, $is_view) as $table) {
            $res[$table] = self::meta_table($table, $language_id, $is_view);
        }
        return $res;
    }

    public static function meta_table($table, string|null $language_id = null, $is_view = false) {
        return ['rows' => self::rows_list($table, $language_id, $is_view),
                'columns' => self::columns_list($table, $language_id, $is_view),
                'sub_columns' => self::sub_columns_list($table, $language_id, $is_view)];
    }

    public static function tables_list(string|null $language_id = null, $is_view = false) {
        $q = self::select('table')->distinct()
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            });
        if ($is_view) {
            $q->whereIn('id', LanguageSound::in_language($language_id));
        }
        return $q->pluck('table');
    }

    public static function rows_list(string $table, string|null $language_id = null, $is_view = false) {
        $q = self::select('row')->distinct()
            ->where('table', '=', $table)
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            });
        if ($is_view) {
            $q->whereIn('id', LanguageSound::in_language($language_id));
        }
        $t = $q->pluck('row');
        return self::select('row')->distinct()->whereIn('row', $t)->pluck('row');
    }

    public static function columns_list(string $table, string|null $language_id = null, $is_view = false) {
        $q = self::select('column')->distinct()
            ->where('table', '=', $table)
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            });
        if ($is_view) {
            $q->whereIn('id', LanguageSound::in_language($language_id));
        }
        $t = $q->pluck('column');
        return self::select('column')->distinct()->whereIn('column', $t)->pluck('column');
    }

    public static function sub_columns_list(string $table, string|null $language_id = null, $is_view = false) {
        $q = self::select('sub_column')->distinct()
            ->where('table', '=', $table)
            ->where(static function(Builder $query) use ($language_id) {
                $query->whereNull('language_id')
                    ->orWhere('language_id', '=', $language_id);
            })
            ->whereNotNull('sub_column');
        if ($is_view) {
            $q->whereIn('id', LanguageSound::in_language($language_id));
        }
        $t = $q->pluck('sub_column');
        return self::select('sub_column')->distinct()->whereIn('sub_column', $t)->pluck('sub_column');
    }
}
