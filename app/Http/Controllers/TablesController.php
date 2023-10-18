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
        ], messages: []);

        $data['name'] ??= '';
        $data['caption'] ??= '';
        $data['order'] = Table::newOrder($language);
        $data['language_id'] = $language->id;

        Table::create($data);

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

        /** @var TableRow $old_row */
        $old_row = $table->rows->last();
        /** @var TableRow $row */
        $row = $table->rows()->create();

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
