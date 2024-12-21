<?php

namespace App\Models;

use League\Csv\Writer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Export extends Model
{
    use HasFactory;

    public function getAllTables($database = 'mysql')
    {
        $excluded_tables = \collect(\explode('|', optional(collect(Cache::get('settings'))
        ->where('key', 'exportable_tables')->first())->properties))
            ->map(fn ($item_1) => \collect(\explode('_', $item_1))
            ->filter(fn ($item_2, $idx) => $idx < (count(\explode('_', $item_1))) - 1)->join("_")) ?? \collect([]);

        $tables = collect(DB::connection($database)->select('SHOW TABLES'))
            ->map(fn ($value) => \array_values((array) $value))
            ->flatten();
        // ->filter(fn ($item, $key) => !\in_array($item, $excluded_tables->toArray()));
        return $tables;
    }

    public function getAllTableColumns($table, $database = 'mysql')
    {
        return !empty($table) ? DB::connection($database)
        ->getSchemaBuilder()
        ->getColumnListing($table) : [];
    }

    public function getTableData($table, $table_columns, $searchParams, $database = 'mysql')
    {

        $db = DB::connection($database)
        ->table($table)->select($table_columns)
            ->when(\in_array("created_at", $table_columns), fn ($q) => $q->latest());
        return $this->filterTable($db, $searchParams);
    }

    public function export($table, $selected_columns, $database = 'mysql')
    {
        $data = DB::connection($database)
        ->table($table)
        ->get($selected_columns);
        $csv = Writer::createFromString('');
        $csv->insertOne(array_keys((array) $data->first())); // Insert column headers
        foreach ($data as $row) {
            $csv->insertOne((array) $row);
        }
        return response()->streamDownload(function () use ($csv, $table) {
            echo $csv->getContent();
        }, $table . '.csv');
    }

    public function readCSV($file)
    {
        if (!empty($file)) {
            return $this->parseCSV($file);
        } else {
            return false;
        }
    }

    private function parseCSV($file)
    {
        $csvData = [];

        $handle = fopen($file->getPathname(), 'r');

        if ($handle !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $csvData[] = $data;
            }
            fclose($handle);
        }

        return $csvData;
    }

    public function filterTable($query, $searchParams)
    {
        if (empty($searchParams) || $searchParams->count() == 0) {
            return $query;
        }
        $searchParams->when(
            $searchParams->filter(fn ($val) => \count($val) > 1)->count() > 0,
            fn ($collection) => $collection->each(function ($section) use (&$query) {
                list($key, $value) = $section;
                $query->where($key, 'LIKE', '%' . $value . '%');
            })
        );

        return $query;
    }
}
