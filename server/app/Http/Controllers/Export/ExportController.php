<?php

namespace App\Http\Controllers\Export;

use App\Models\Export;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;

class ExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:can export');
    }

    public function index()
    {
        $export = new Export();
        $table = \request()->get('table');
        $database = \request()->get('database');
        $selected_table_columns = \request()->get('table_columns', []);
        $data_limit = request()->get('data_limit');
        $order_by = request()->get('order_by');
        $order_by_type = request()->get('order_by_type');
        $databases =
            collect(Cache::get('settings'))
            ->where('key', 'database_configuration')->first()
            ->getSettingValue()->keys();
        $table_columns = [];
        $table_data = [];

        $search = \request()->get('search', '||');

        $searchParams = collect(explode('|', $search))
            ->filter(fn ($section) => !empty($section)) // Filter out empty sections
            ->map(function ($section) {
                return explode(':', $section);
            });


        if (!empty($table) && !empty($database)) {
            $table_columns = $export->getAllTableColumns($table, $database);
        }

        if (\count($selected_table_columns) > 0) {
            $table_validation = \collect($selected_table_columns)
                ->filter(function ($item) use ($table_columns) {
                    return in_array($item, $table_columns);
                })->count() < \count($selected_table_columns);

            $failed_keys
                = \collect($selected_table_columns)
                ->filter(function ($item) use ($table_columns) {
                    return !in_array($item, $table_columns);
                })->join(',');

            if ($table_validation) {
                return \redirect()->back()->withErrors(['table_error' => "$table does not contain $failed_keys."]);
            }
            $table_data = $export->getTableData($table, $selected_table_columns, $searchParams, $database)
            ->when($data_limit > 0, fn ($q) => $q->limit($data_limit));

            // if(!empty($order_by)) {
            //     $table_data->orderBy($order_by, $order_by_type);
            // }
            $table_data = $table_data->get($selected_table_columns);
        } else {
            try {
                $table_data = empty($table) ?: $export->getTableData($table, ['*'], $searchParams, $database)
                ->when($data_limit > 0, fn ($q) => $q->limit($data_limit))
                // ->when(!empty($order_by) && !empty($order_by_type), fn ($q) => $q->orderBy($order_by_type, $order_by))
                ->get();
                $selected_table_columns = $table_columns;
            } catch (\Throwable $th) {
                return \redirect()->back()->withErrors(['table_error' => "$database database does not contain the $table table."]);
            }

        }

        $searchPlaceholder = \collect($selected_table_columns)
            ->map(function ($key, $idx) use ($selected_table_columns) {
                if ($idx == 0) {
                    return '|' . $key . ":search here";
                }
                if ($idx + 1 == count($selected_table_columns)) {
                    return $key . ":search here|";
                }
                return $key . ":search here";
            })->join('|');

        Cache::set('current_table_data_for_export', ['selected_columns' => $selected_table_columns, 'table' => $table, 'database' => $database]);

        return \view('Export.View', [
            'tables' => $export->getAllTables($database),
            'table_columns' => $table_columns,
            'table_data' => $table_data,
            'selected_table_columns' => $selected_table_columns,
            'table_error' => !empty($table) ?: "Please select a valid table .",
            'searchPlaceholder' => $searchPlaceholder,
            'table_data_count_overall' => $export->getTableData($table, ['*'], \collect([]), $database)->count(),
            'search' => request()->get('search'),
            'databases' => $databases
        ]);
    }

    public function export()
    {
        $export = new Export();
        $current_export_data = Cache::get('current_table_data_for_export');
        // \dd($current_export_data);
        Cache::forget('current_table_data_for_export');
        Session::flash('message', 'The data was export successfully.');
        Session::flash('alert-class', 'alert-success');

        return $export->export($current_export_data['table'], $current_export_data['selected_columns'], $current_export_data['database']);
    }
}
