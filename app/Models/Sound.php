<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
 * @property-read Language|null $language
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LanguageSound> $language_sounds
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
 * @property-read int|null $language_sounds_count
 * @mixin \Eloquent
 */
class Sound extends Model
{
    use HasFactory;

    protected $table = 'sounds';
    protected $fillable = ['sound', 'table', 'row', 'column', 'sub_column', 'language_id'];

    public function language() {
        return $this->belongsTo(Language::class);
    }

    public function language_sounds() {
        return $this->hasMany(LanguageSound::class, 'sound_id', 'id');
    }

    public static function sounds($language_id) {
        $collection = self::with([
            'language_sounds',
            'language_sounds.sound',
            'language_sounds.allophone',
            'language_sounds.allophone.sound',
        ])->whereNull('language_id');

        if ($language_id) {
            $collection = $collection->orWhere('language_id', '=', $language_id);
        }

        return $collection->get();
    }

    public static function view_sounds($language_id) {
        $collection = self::sounds($language_id);

        $collection = $collection->reject(static function(self $sound) use ($language_id) {
            return $sound->language_sounds->where('language_id', $language_id)->count() === 0;
        });

        return $collection;
    }

    public static function meta(string|null $language_id = null, $is_view = false) {
        $res = [];

        $collection = self::with('language_sounds')
                ->whereNull('language_id')
                ->orWhere('language_id', '=', $language_id)
                ->get();

        if ($is_view) {
            $collection = $collection->reject(static function(self $sound) use ($language_id) {
                return $sound->language_sounds->where('language_id', $language_id)->count() === 0;
            });
        }

        foreach (self::tables_list($collection) as $table) {
            $res[$table] = self::meta_table($collection, $table);
        }
        return $res;
    }

    public static function meta_table(Collection $collection, $table) {
        $collection = $collection->where('table', $table);
        return ['rows' => self::rows_list($collection),
                'columns' => self::columns_list($collection),
                'sub_columns' => self::sub_columns_list($collection)];
    }

    public static function tables_list(Collection $collection) {
        return $collection->unique('table')->pluck('table');
    }

    public static function rows_list(Collection $collection) {
        $t = $collection->unique('row')->pluck('row');
        return self::select('row')->distinct()->whereIn('row', $t)->pluck('row');
    }

    public static function columns_list(Collection $collection) {
        $t = $collection->unique('column')->pluck('column');
        return self::select('column')->distinct()->whereIn('column', $t)->pluck('column');
    }

    public static function sub_columns_list(Collection $collection) {
        $t = $collection->unique('sub_column')->pluck('sub_column');
        return self::select('sub_column')->distinct()->whereIn('sub_column', $t)->pluck('sub_column');
    }
}
