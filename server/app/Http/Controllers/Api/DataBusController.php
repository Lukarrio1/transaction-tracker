<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use App\Models\DynamicModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Validator;

class DataBusController extends Controller
{
    public $orderByTypes = [
        "asc",
        'desc'
    ];

    public $methods = ["manyRecords", "oneRecord", "checkRecord", "deleteRecord", "saveRecord", "updateRecord", "consumeGetEndPoint"];
    public function __call($method, $parameters)
    {
        $this->data_interoperability = (bool)\getSetting('data_interoperability');

        $method_to_call = \in_array(collect(explode('_', $method))->first(), $this->methods)
            ? collect(explode('_', $method))->first()
            : $method;
        if (\in_array(collect(explode('_', $method))->first(), $this->methods) && $this->data_interoperability == true) {
            return $this->$method_to_call($method, $parameters);
        }
        return response()->json(['error' => 'Resource not found.'], 404);
    }


    /**
     * Method oneRecord
     *
     * @param $method string [This refers to the name of the method stored in the database ef oneRecord_jdbais]
     *
     * @return JsonResponse
     */
    public function oneRecord($method): JsonResponse
    {
        $item = [];
        $currentRouteNode = $this->getCurrentRoute();
        $route_parameters = \collect(Route::current()->parameters());
        $database = $currentRouteNode->properties['value']->node_database;
        $table = $currentRouteNode->properties['value']->node_table;
        $node_table_columns =
            $currentRouteNode->properties['value']->node_table_columns;
        $node_cache_ttl = $currentRouteNode->properties['value']->node_cache_ttl ?? 0;
        $id = request()->user()->id ?? null;
        $cache_name
            = $method . '_' . $database . '_' . $table . '_' .  $route_parameters->map(fn ($value, $key) => $key . '_' . Str::lower($value))->join('_') . "_user_" . $id;
        if (!Cache::has($cache_name)) {
            if ($database != null && $table != null) {
                $item =  DB::connection($database)
                    ->table($table)
                    ->select($node_table_columns);
                if (isset($currentRouteNode->properties['value']->node_item)) {
                    $item->where('id', $currentRouteNode->properties['value']->node_item);
                } else {
                    $route_parameters->each(fn ($value, $key) => $item->when($value != $this->search_skip_word, fn ($q) => $q->where($key, "LIKE", "%" . $value . "%")));
                }
                $relationShips = $this->handleJoins($currentRouteNode);
                if (count($relationShips) > 0) {
                    $item = $this->addNestedRelationship($item->get(), $currentRouteNode, $database)->first();
                } else {
                    $item = $item->first();
                }
                Cache::add($cache_name, $item, $node_cache_ttl);
            }
        } else {
            $item =  Cache::get($cache_name);
        }
        return \response()->json($item, 200);
    }

    /**
     * Method manyRecords
     *
     *@param $method string [This refers to the name of the method stored in the database ef manyRecords_jdbais]
     *
     * @return JsonResponse
     */
    public function manyRecords($method): JsonResponse
    {
        $currentRouteNode = $this->getCurrentRoute();
        $route_parameters = \collect(Route::current()->parameters());
        if (!$currentRouteNode) {
            return response()->json([]);
        }
        $properties = $currentRouteNode->properties['value'];
        $database = optional($properties)->node_database;
        $table = optional($properties)->node_table;
        $columns = optional($properties)->node_table_columns ?? ['*'];
        $limit = (int) optional($properties)->node_data_limit;
        $orderByField = optional($properties)->node_order_by_field;
        $orderByType = optional($properties)->node_order_by_type;
        $node_cache_ttl = optional($properties)->node_cache_ttl ?? 0;
        $id = request()->user()->id ?? null;
        $cache_name
            = $method . '_' . $database . '_' . $table . '_' .  $route_parameters->map(fn ($value, $key) => $key . '_' . Str::lower($value))->join('_') . "_user_" . $id;
        $items = [];
        if (!Cache::has($cache_name)) {
            if (!$database || !$table) {
                return response()->json([]);
            }
            $query = DB::connection($database)
                ->table($table)
                ->select($columns);

            if ($limit > 0) {
                $query->limit($limit);
            }
            if ($orderByField && $orderByType) {
                $query->orderBy($orderByField, $orderByType);
            }
            if ($route_parameters->count() > 0) {
                $route_parameters->each(fn ($value, $key) => $query->when($value != $this->search_skip_word, fn ($q) => $q->where($key, "LIKE", "%" . $value . "%")));
            }
            $items = $query->get();

            $relationShips = $this->handleJoins($currentRouteNode);
            if (count($relationShips) > 0) {
                $items = $this->addNestedRelationship($items, $currentRouteNode, $database);
            }
            Cache::add($cache_name, $items, $node_cache_ttl);
        } else {
            $items = Cache::get($cache_name);
        }
        return \response()->json($items, 200);
    }

    // public function manyRecords($method): JsonResponse
    // {
    //     $currentRouteNode = $this->getCurrentRoute();
    //     $route_parameters = \collect(Route::current()->parameters());
    //     if (!$currentRouteNode) {
    //         return response()->json([]);
    //     }
    //     $properties = $currentRouteNode->properties['value'];
    //     $database = optional($properties)->node_database;
    //     $table = optional($properties)->node_table;
    //     $columns = optional($properties)->node_table_columns ?? ['*'];
    //     $limit = (int) optional($properties)->node_data_limit;
    //     $orderByField = optional($properties)->node_order_by_field;
    //     $orderByType = optional($properties)->node_order_by_type;
    //     $node_cache_ttl = optional($properties)->node_cache_ttl ?? 0;
    //     $id = request()->user()->id ?? null;
    //     $cache_name
    //         = $method . '_' . $database . '_' . $table . '_' .  $route_parameters->map(fn ($value, $key) => $key . '_' . Str::lower($value))->join('_') . "_user_" . $id;

    //     $items = Cache::flexible($cache_name, [5,$node_cache_ttl], function () use ($database, $table, $columns, $limit, $orderByField, $orderByType, $route_parameters, $currentRouteNode) {
    //         $query = DB::connection($database)
    //                        ->table($table)
    //                        ->select($columns);
    //         if (!$database || !$table) {
    //             return [];
    //         }

    //         if ($limit > 0) {
    //             $query->limit($limit);
    //         }
    //         if ($orderByField && $orderByType) {
    //             $query->orderBy($orderByField, $orderByType);
    //         }
    //         if ($route_parameters->count() > 0) {
    //             $route_parameters->each(fn ($value, $key) => $query->when($value != $this->search_skip_word, fn ($q) => $q->where($key, "LIKE", "%" . $value . "%")));
    //         }
    //         $items = $query->get();

    //         $relationShips = $this->handleJoins($currentRouteNode);
    //         if (count($relationShips) > 0) {
    //             $items = $this->addNestedRelationship($items, $currentRouteNode, $database);
    //         }
    //         return $items;

    //     });

    //     return \response()->json($items, 200);
    // }



    /**
     * Method checkRecord
     *
     * @return JsonResponse
     */
    public function checkRecord(): JsonResponse
    {

        $currentRouteNode = $this->getCurrentRoute();
        $route_parameters = \collect(Route::current()->parameters());
        $database = $currentRouteNode->properties['value']->node_database;
        $table = $currentRouteNode->properties['value']->node_table;
        $node_table_columns =
            $currentRouteNode->properties['value']->node_table_columns;
        if ($database != null && $table != null) {
            $item =  DB::connection($database)
                ->table($table)
                ->select($node_table_columns);
            if (isset($currentRouteNode->properties['value']->node_item)) {
                $item->where('id', $currentRouteNode->properties['value']->node_item);
            } else {
                $route_parameters->each(fn ($value, $key) => $item->when($value != $this->search_skip_word, fn ($q) => $q->where($key, $value)));
            }
            $item = $item->first();
        } else {
            $item = [];
        }

        return \response()->json(["exist" => !empty($item)], 200);
    }

    /**
     * Method deleteRecord
     *
     * @return JsonResponse
     */
    public function deleteRecord(): JsonResponse
    {
        $currentRouteNode = $this->getCurrentRoute();
        $route_parameters = \collect(Route::current()->parameters());
        $database = $currentRouteNode->properties['value']->node_database;
        $table = $currentRouteNode->properties['value']->node_table;
        if ($database != null && $table != null) {
            $item =  DB::connection($database)
                ->table($table);
            $route_parameters->each(fn ($value, $key) => $item->when($value != $this->search_skip_word, fn ($q) => $q->where($key, $value)));
            $item->delete();
        } else {
            $item = null;
        }

        return \response()->json([], 204);
    }

    /**
     * Method saveRecord
     *
     * @return JsonResponse
     */
    public function saveRecord(): JsonResponse
    {
        $currentRouteNode = $this->getCurrentRoute();
        $database = $currentRouteNode->properties['value']->node_database;
        $table = $currentRouteNode->properties['value']->node_table;
        $node_table_columns =
            $currentRouteNode->properties['value']->node_table_columns;
        $rules = [];
        for ($i = 0; $i < \count($node_table_columns); $i++) {
            $rules[$node_table_columns[$i]] = collect($currentRouteNode->properties['value']->{'node_endpoint_field_' . $node_table_columns[$i]})
                ->join(',');
        }
        $data = \request()->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return  \response()->json(['errors' => $validator->errors()]);
        }
        if ($database != null && $table != null) {
            DB::connection($database)
                ->table($table)
                ->insert($data);
            $item =
                DB::connection($database)
                ->table($table)
                ->orderBy('id', 'desc')
                ->first();
        } else {
            $item = [];
        }

        return \response()->json($item, 201);
    }

    /**
     * Method updateRecord
     *
     * @return JsonResponse
     */
    public function updateRecord(): JsonResponse
    {
        $currentRouteNode = $this->getCurrentRoute();
        $database = $currentRouteNode->properties['value']->node_database;
        $route_parameters = \collect(Route::current()->parameters());
        $table = $currentRouteNode->properties['value']->node_table;
        $node_table_columns =
            $currentRouteNode->properties['value']->node_table_columns;
        $rules = [];
        for ($i = 0; $i < \count($node_table_columns); $i++) {
            $rules[$node_table_columns[$i]] = collect($currentRouteNode->properties['value']->{'node_endpoint_field_' . $node_table_columns[$i]})
                ->join(',');
        }
        $data = \request()->all();
        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return  \response()->json(['errors' => $validator->errors()]);
        }
        if ($database != null && $table != null) {
            $query =  DB::connection($database)
                ->table($table);
            $route_parameters->each(fn ($value, $key) => $query->when($value != $this->search_skip_word, fn ($q) => $q->where($key, $value)));
            $query->update($data);
            $item =
                DB::connection($database)
                ->table($table)
                ->orderBy('id', 'desc')
                ->first();
        } else {
            $item = [];
        }

        return \response()->json($item, 201);
    }


    /**
     * Method consumeGetEndPoint
     *
     * @return JsonResponse
     */
    public function consumeGetEndPoint(): JsonResponse
    {
        $currentRouteNode = $this->getCurrentRoute();
        $route_parameters = \collect(Route::current()->parameters());
        $node_endpoint_to_consume = $currentRouteNode->properties['value']->node_endpoint_to_consume;
        $node_item_display_aid = $currentRouteNode->properties['value']->node_item_display_aid;
        $node_table_columns = $currentRouteNode->properties['value']->node_table_columns;
        $response = $this->getHttpData($node_endpoint_to_consume);
        $data = collect(!empty($response) && isset($node_item_display_aid) ? $response[$node_item_display_aid] : $response)
            ->map(function ($item) use ($node_table_columns) {
                $temp = [];
                if (count($node_table_columns) > 0) {
                    foreach ($node_table_columns as $column) {
                        $temp[$column] = $item[$column];
                    }
                } else {
                    $temp = $item;
                }
                return $temp;
            })->filter(function ($item) use ($route_parameters) {
                return count($route_parameters) > 0 ? $route_parameters
                    ->filter(fn ($rp, $key) => Str::contains(Str::lower($item[$key]), Str::lower($route_parameters->get($key))))
                    ->count() > 0 : true;
            })
            ->all();
        return response()->json($data, 200);
    }
}
