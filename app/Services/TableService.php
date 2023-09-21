<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Collection;

class TableService
{
    function tables($language, Collection $data, $meta) {
        $tables = [];


        foreach ($meta as $table_name => $table_meta) {
            $table_data = $data->where('table', '=', $table_name);

            $table = [];

            $table['name'] = $table_name;

            $table['rows'] = [];
            $first_row = [];
            $first_row[] = ['data' => $table_name, 'header' => true];
            foreach ($table_meta['columns'] as $column_name) {
                $colspan = count($table_meta['sub_columns']);

                $sub_column_names = $table_meta['sub_columns']->isEmpty() ? [null] : $table_meta['sub_columns'];
                foreach ($sub_column_names as $sub_column_name) {
                    $all_sub_column_data = $table_data->where('column', $column_name)
                        ->whereIn('sub_column', [$sub_column_name, null]);
                    if ($all_sub_column_data->isEmpty()) {
                        $colspan -= 1;
                    }
                }
                $first_row[] = [
                    'data' => $column_name,
                    'header' => true,
                    'colspan' => $colspan,
                ];
            }
            $table['rows'][] = $first_row;

            foreach ($table_meta['rows'] as $row_name) {
                $row_data = $table_data->where('row', '=', $row_name);
                $row = [];
                $row[] = ['data' => $row_name, 'header' => true];

                foreach ($table_meta['columns'] as $column_name) {
                    $column_data = $row_data->where('column', '=', $column_name);
                    $sub_column_names = $table_meta['sub_columns']->isEmpty() ? [null] : $table_meta['sub_columns'];

                    foreach ($sub_column_names as $sub_column_name) {
                        $sub_column_data = $column_data->where('sub_column', '=', $sub_column_name);
                        $td_data = $sub_column_data->isNotEmpty() ? $sub_column_data->first() : ($column_data->first()?->sub_column == null ? $column_data->first() : null);

                        $all_sub_column_data = $table_data->where('column', $column_name)
                            ->whereIn('sub_column', [$sub_column_name, null]);
                        if ($all_sub_column_data->isEmpty()) {
                            continue;
                        }

                        $colspan = 1;
                        if ($td_data && $td_data->sub_column == null) {
                            $colspan = count($table_meta['sub_columns']) > 0 ? count($table_meta['sub_columns']) : 1;
                        }
                        if ($td_data) {

                            $lsound = $td_data->language_sound($language->id);
                            if ($lsound->allophone_of) {
                                $str = "[" . $td_data->sound . "] /" . $td_data->language_sound($language->id)->allophone->sound->sound . "/";
                            } else {
                                $str = $td_data->sound;
                            }

                            $row[] = ['data' => $str, 'colspan' => $colspan];
                        } else {
                            $row[] = ['data' => null];
                        }

                        if ($td_data && $td_data->sub_column == null) {
                            break;
                        }
                    }
                }

                $table['rows'][] = $row;
            }

            $tables[] = $table;
        }

        return $tables;
    }
}