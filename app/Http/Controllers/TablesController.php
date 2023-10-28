<?php

namespace App\Http\Controllers;

use App\Models\Language;
use App\Models\Table;
use App\Models\TableCell;
use App\Models\TableCellStyle;
use App\Models\TableRow;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TablesController extends Controller
{
    function index(Request $request, $code) {
        $language = Language::with([
            'tables',
            'tables.rows',
            'tables.rows.cells',
            'tables.rows.cells.styles',
        ])->findOrFail($code);

        $editMode = $request->has('editMode');

        return Inertia::render('LanguageTables', compact('language', 'editMode'));
    }

    function store(Request $request, $code) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);

        $data = $request->validate([
            'name' => 'nullable',
            'caption' => 'nullable',
            'height' => 'nullable|integer',
            'width' => 'nullable|integer',
        ], messages: [
            'height.integer' => 'Высота должна быть числом.',
            'width.integer' => 'Ширина должна быть числом.',
        ]);

        $data['name'] ??= '';
        $data['caption'] ??= '';
        $data['order'] = Table::newOrder($language);

        $data['height'] ??= 2;
        $data['width'] ??= 2;

        /** @var Table $table */
        $table = $language->tables()->create($data);

        for ($i = 0; $i < $data['height']; $i++) {
            /** @var TableRow $row */
            $row = $table->rows()->create(['order' => TableRow::newOrder($table)]);

            for ($j = 0; $j < $data['width']; $j++) {
                $row->cells()->create(['content' => '', 'order' => TableCell::newOrder($row)]);
            }
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function update(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        $data = $request->validate([
            'name' => 'nullable',
            'caption' => 'nullable',
        ], messages: []);

        $data['name'] ??= '';
        $data['caption'] ??= '';

        $table->update($data);

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function delete(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        $table->delete();

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function add_row(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        ['after' => $after] = $request->validate([
            'after' => 'nullable|integer',
        ], messages: [
            'after.integer' => 'Положение должно быть числом.'
        ]);

        $order = $after ? $after + 1 : TableRow::newOrder($table);

        if ($after) {
            $table->rows()->where('order', '>', $after)->increment('order');
        }

        /** @var TableRow $old_row */
        $old_row = $table->rows->last();
        /** @var TableRow $row */
        $row = $table->rows()->create(compact('order'));

        if ($old_row) {
            $row->cells()->createMany($old_row->cells->map(static function (TableCell $cell) {
                $cell = $cell->toArray();
                $cell['content'] = '';
                return $cell;
            }));
            // TODO: styles copy?
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function add_column(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        ['after' => $after] = $request->validate([
            'after' => 'nullable|integer',
        ], messages: [
            'after.integer' => 'Положение должно быть числом.'
        ]);

        $rows = $table->rows;

        foreach ($rows as $row) {
            $order = $after ? $after + 1 : TableCell::newOrder($row);
            if ($after) {
                $row->cells()->where('table_cells.order', '>', $after)->increment('table_cells.order');
            }

            $row->cells()->create(['content' => '', 'order' => $order]);
            // TODO: styles copy?
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function delete_row(Request $request, $code, $table_id, $row_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);
        /** @var TableRow $row */
        $row = TableRow::findOrFail($row_id);
        Gate::authorize('all-is-language', $row->table);

        $row->delete();

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function add_cell(Request $request, $code, $table_id, $row_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);
        /** @var TableRow $row */
        $row = TableRow::findOrFail($row_id);
        Gate::authorize('all-is-language', $row->table);

        $data = $request->validate([
            'content' => 'nullable',
            'is_header' => 'nullable',
        ], messages: []);

        $data['content'] ??= '';
        $data['is_header'] ??= 0;

        $data['order'] = TableCell::newOrder($row);

        $row->cells()->create($data);

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function update_cell(Request $request, $code, $table_id, $cell_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);
        /** @var TableCell $cell */
        $cell = TableCell::findOrFail($cell_id);
        Gate::authorize('all-is-language', $cell->table);

        $data = $request->validate([
            'content' => 'nullable',
            'is_header' => 'nullable',
            'rowspan' => 'nullable',
            'colspan' => 'nullable',
        ], messages: []);

        $data['content'] ??= $cell->content;
        $data['is_header'] ??= $cell->is_header;
        $data['rowspan'] ??= $cell->rowspan;
        $data['colspan'] ??= $cell->colspan;

        $cell->update($data);

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }


    function update_cell_content(Request $request, $code, $table_id, $cell_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);
        /** @var TableCell $cell */
        $cell = TableCell::findOrFail($cell_id);
        Gate::authorize('all-is-language', $cell->table);

        $data = $request->validate([
            'content' => 'nullable',
        ], messages: []);

        $data['content'] ??= '';

        $cell->update($data);

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }


    function update_merge(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        $data = $request->validate([
            'cells' => 'required|array',
        ], messages: [
            'cells.required' => 'Необходимо указать ячейки.',
            'cells.array' => 'Ячейки надо указать массивом.',
        ]);

        $cells = TableCell::with('tableRow')->findOrFail($data['cells']);

        $cells = $cells->reduce(static function(Collection $res, TableCell $val) {
            $res->push($val);
            return $res->merge($val->mergedCells()->with('tableRow')->get());
        }, new Collection());

        // Находим левый верхний угол
        /** @var TableCell $topleft */
        $topleft = $cells->reduce(static function(TableCell|null $min, TableCell $val) {
            if ($min === null || $val->tableRow->order < $min->tableRow->order) {
                return $val;
            } else if ($val->tableRow->order === $min->tableRow->order) {
                if ($val->order < $min->order) {
                    return $val;
                } else {
                    return $min;
                }
            }
            return $min;
        });


        // Находим задействованные строки
//        /** @var Collection $rows */
//        $rows = $cells->reduce(static function(Collection $min, TableCell $val) {
//            if (!$min->contains($val->tableRow)) {
//                $min->push($val->tableRow);
//            }
//            return $min;
//        }, new Collection());
        $rows = $cells->reduce(static function(array $min, TableCell $val) {
            if (!array_key_exists($val->tableRow->id, $min)) {
                $min[$val->tableRow->id] = new Collection([$val]);
            } else {
                $min[$val->tableRow->id]->push($val);
            }
            return $min;
        }, []);

        $topleft->rowspan = count($rows);
        $topleft->colspan = $rows[$topleft->tableRow->id]->count();

        foreach ($cells as $cell) {
            /** @var TableCell $cell */
            $cell->merged_in = $topleft->id;
            if ($cell->id !== $topleft->id) {
                $cell->rowspan = 1;
                $cell->colspan = 1;
            }
            $cell->save();
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function update_unmerge(Request $request, $code, $table_id, $cell_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);
        /** @var TableCell $cell */
        $cell = TableCell::findOrFail($cell_id);
        Gate::authorize('all-is-language', $cell->table);

        $cell->rowspan = 1;
        $cell->colspan = 1;
        $cell->merged_in = null;
        $cell->save();

        foreach ($cell->mergedCells()->get() as $cell) {
            $cell->merged_in = null;
            $cell->save();
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }



    function delete_cell(Request $request, $code, $table_id, $cell_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);
        /** @var TableCell $cell */
        $cell = TableCell::findOrFail($cell_id);
        Gate::authorize('all-is-language', $cell->table);

        $cell->delete();

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function update_style(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        $data = $request->validate([
            'cells' => 'required|array',
            'style' => 'required',
            'value' => 'nullable',
        ], messages: [
            'cells.required' => 'Требуется указать ячейки.',
            'cells.array' => 'Требуется указать ячейки массивом.',
            'style.required' => 'Указать что за стиль обязательно.',
        ]);

        $cells = TableCell::with(['tableRow', 'tableRow.table', 'styles'])->findOrFail($data['cells']);

        foreach ($cells as $cell) {
            Gate::authorize('all-is-language', $cell->table);
        }

        foreach ($cells as $cell) {
            $style = $cell->styles->where('style', $data['style'])->first();
            if ($style) {
                if ($data['value']) {
                    // Если есть стиль и указано новое значение, меняем значение
                    $style->update($data);
                } else {
                    // Если стиль указан, а новое значение нет, удаляем стиль
                    $style->delete();
                }
            } else if ($data['value']) {
                // Если указан стиль которого нет и указано значение, добавляем стиль
                $cell->styles()->create($data);
            }
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }

    function toggle_style(Request $request, $code, $table_id) {
        /** @var Language $language */
        $language = Language::with('tables')->findOrFail($code);
        Gate::authorize('edit-language', $language);
        $table = Table::findOrFail($table_id);
        Gate::authorize('all-is-language', $table);

        $data = $request->validate([
            'cells' => 'required|array',
            'style' => 'required',
            'value' => 'nullable',
        ], messages: [
            'cells.required' => 'Требуется указать ячейки.',
            'cells.array' => 'Требуется указать ячейки массивом.',
            'style.required' => 'Указать что за стиль обязательно.',
        ]);

        $cells = TableCell::with(['tableRow', 'tableRow.table', 'styles'])->findOrFail($data['cells']);

        foreach ($cells as $cell) {
            Gate::authorize('all-is-language', $cell->table);
        }

        foreach ($cells as $cell) {
            $style = $cell->styles->where('style', $data['style'])->first();
            if ($style) {
                if ($data['value'] && $style->value !== $data['value']) {
                    // Если есть стиль и указано новое другое значение, меняем значение
                    $style->update($data);
                } else if ($data['value'] && $style->value === $data['value']) {
                    // Если стиль указан, с таким же значением, удаляем стиль
                    $style->delete();
                }
            } else if ($data['value']) {
                // Если указан стиль которого нет и указано значение, добавляем стиль
                $cell->styles()->create($data);
            }
        }

        $editMode = $request->has('editMode');
        return redirect()->route('languages.tables', ['code' => $language->id, 'editMode' => $editMode]);
    }
}
