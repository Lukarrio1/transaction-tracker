<?php

namespace App\Http\Controllers\Permission;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Permission\PermissionSaveRequest;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:can crud permissions');
    }

    public function index($permission = null)
    {
        $translate = [
            'name' => 'name',
        ];

        $translateExamples = [
            'name' => 'can view home page',
        ];

        $search = \request()->get('search', '||');
        $searchParams = collect(explode('|', $search))
            ->filter(fn ($section) => !empty($section)) // Filter out empty sections
            ->map(function ($section) {
                return explode(':', $section);
            });
        // Build the search placeholder
        $searchPlaceholder = \collect($translate)->keys()->map(function ($key, $idx) use ($translate, $translateExamples) {
            if ($idx == 0) {
                return '|' . $key . ":$translateExamples[$key]";
            }
            if ($idx + 1 == count($translate)) {
                return $key . ":$translateExamples[$key]|";
            }
            return $key . ":$translateExamples[$key]";
        })->join('|');


        $permissions = Permission::query();
        $searchParams->when(
            $searchParams->filter(fn ($val) => \count($val) > 1)->count() > 0,
            fn ($collection) => $collection->each(function ($section) use ($permissions, $translate) {
                list($key, $value) = $section;
                // Check if the key is valid in the translation map
                if (!isset($translate[$key])) {
                    return; // Skip invalid keys
                }
                $permissions->where($translate[$key], 'LIKE', '%' . $value . '%'); // Apply the condition to the query
            })
        );
        $permissions_count = $permissions->count();
        $permissions_count_overall
            = Permission::query()->count();
        $max_amount_of_pages = $permissions_count / 12;
        request()->merge(['page' => \request('page') == null || (int) \request('page') < 1 ? 1 : ((int)\request('page') > $max_amount_of_pages ? \ceil($max_amount_of_pages) : \request('page'))]);
        $permissions = $permissions->latest()
            ->skip((int)12 * (int)  \request('page') - (int)12)
            ->take((int)12)->get();
        return view('Permission.View', [
            'permissions' => $permissions,
            'permissions_count' => $permissions_count,
            'permission' => $permission,
            'page_count' => \ceil($max_amount_of_pages),
            'permissions_count_overall' => $permissions_count_overall,
            'search' => \request('search'),
            'searchPlaceholder' => $searchPlaceholder

        ]);
    }

    public function save(PermissionSaveRequest $request)
    {
        Permission::updateOrCreate(['id' => $request->id], $request->all() + ['guard' => 'api']);
        Session::flash('message', 'The permission was saved successfully.');
        Session::flash('alert-class', 'alert-success');
        return \redirect()->route('viewPermissions');
    }

    public function edit(Permission $permission)
    {
        return $this->index($permission);
    }

    public function delete(Permission $permission)
    {
        $permission->delete();
        Session::flash('message', 'The permission was deleted successfully.');
        Session::flash('alert-class', 'alert-success');
        return \redirect()->route('viewPermissions');
    }
}
