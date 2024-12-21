<?php

namespace App\Http\Controllers\Role;

use App\Models\Setting;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Spatie\Permission\Models\Permission;
use App\Http\Requests\Role\RoleSaveRequest;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:can crud roles');
    }

    public function index($role = null)
    {

        $translate = [
            'name' => 'name',
            'permission' => 'name',
        ];

        $translateExamples = [
            'name' => 'Admin',
            'permission' => 'can visit home page',
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


        $roles = Role::query()->with('permissions');
        $roles_count_overall = $roles->count();
        $searchParams->when(
            $searchParams->filter(fn ($val) => \count($val) > 1)->count() > 0,
            fn ($collection) => $collection->each(function ($section) use ($roles, $translate) {
                list($key, $value) = $section;
                // Check if the key is valid in the translation map
                if (!isset($translate[$key])) {
                    return; // Skip invalid keys
                }
                // Convert 'type' value to its corresponding node type ID
                if ($key === 'permission') {
                    $convertedValue = $value;
                } else {
                    $convertedValue = $value;
                }
                if ($translate[$key] === 'name') {
                    $roles->whereHas('permissions', fn ($q) => $q->where($translate[$key], 'LIKE', '%' . $convertedValue . '%')); // Apply the condition to the query)
                } else {
                    $roles->where($translate[$key], 'LIKE', '%' . $convertedValue . '%'); // Apply the condition to the query

                }
            })
        );
        $roles_count = $roles->count();
        $max_amount_of_pages = $roles_count / 5;
        \request()->merge(['page' => \request('page') == null || (int) \request('page') < 1 ? 1 : ((int)\request('page') > $max_amount_of_pages ? \ceil($max_amount_of_pages) : \request('page'))]);
        $setting = \optional(Setting::where('key', 'admin_role')->first())->getSettingValue();
        $role_for_checking = !empty($setting) ? Role::find((int)$setting) : null;
        $permissions = Permission::all();
        $roles = $roles
            ->when(!\request()->user()->hasRole($role_for_checking), fn ($q) => $q->where('priority', '>', Role::min('priority')))
            ->skip((int) 5 * (int) \request('page') - (int) 5)
            ->take((int) 5)
            ->orderBy('priority', 'asc')
            ->get()
            ->map(fn ($role) => [
                ...$role->toArray(),
                'permission_name' => collect($role->permissions)->map(fn ($permission) => $permission->name)
            ]);
        return view('Role.View', [
            'role' => optional($role)->load('permissions'),
            'roles' => $roles,
            'permissions' => $permissions,
            'roles_count' => $roles_count,
            'page_count' => \ceil($max_amount_of_pages),
            'search' => \request('search'),
            'roles_count_overall' => $roles_count_overall,
            'searchPlaceholder' => $searchPlaceholder
        ]);
    }

    public function save(RoleSaveRequest $request)
    {
        Role::updateOrCreate(['id' => $request->id], $request->all() + ['guard' => 'api'])
            ->syncPermissions(Permission::whereIn('id', $request->get('permissions', []))
                ->pluck('name')->toArray());
        Session::flash('message', 'The role was saved successfully.');
        Session::flash('alert-class', 'alert-success');
        return \redirect()->route('viewRoles');
    }

    public function edit(Role $role)
    {
        return $this->index($role);
    }

    public function delete(Role $role)
    {
        $role->delete();
        Session::flash('message', 'The role was deleted successfully.');
        Session::flash('alert-class', 'alert-success');
        return \redirect()->route('viewRoles');
    }
}
