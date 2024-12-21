<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\User\UserUpdateRequest;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:can crud users');
    }




    public function index()
    {
        $translate = [
            'name' => 'name',
            'email' => 'email',
            'role' => 'name',
        ];

        $translateExamples = [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'role' => 'admin',
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

        $users = User::query()->with('roles');
        $setting = \optional(Setting::where('key', 'admin_role')->first())->getSettingValue();
        $role_for_checking = !empty($setting) ? Role::find((int)$setting) : null;
        $roles = Role::query()
            ->when(!\request()->user()->hasRole($role_for_checking), fn ($q) => $q->where('priority', '>', Role::min('priority')))
            ->get();

        $searchParams->when(
            $searchParams->filter(fn ($val) => \count($val) > 1)->count() > 0,
            fn ($collection) => $collection->each(function ($section) use ($users, $translate) {
                list($key, $value) = $section;
                // Check if the key is valid in the translation map
                if (!isset($translate[$key])) {
                    return; // Skip invalid keys
                }
                // Convert 'type' value to its corresponding node type ID
                if ($translate[$key] === 'role') {
                    $convertedValue = $value;
                } else {
                    $convertedValue = $value;
                }
                if ($translate[$key] === 'role') {
                    $users->whereHas('roles', fn ($q) => $q->where($translate[$key], 'LIKE', '%' . $convertedValue . '%')); // Apply the condition to the query)
                } else {
                    $users->where($translate[$key], 'LIKE', '%' . $convertedValue . '%'); // Apply the condition to the query

                }
            })
        );
        $users_count = $users->count();
        $max_amount_of_pages = $users_count / 8;
        \request()->merge(['page' => \request('page') == null || (int) \request('page') < 1 ? 1 : ((int)\request('page') > $max_amount_of_pages ? \ceil($max_amount_of_pages) : \request('page'))]);
        $users = $users->latest('updated_at')->customPaginate(8, \request('page'))->get();

        return \view('User.View', [
            'users' => $users->map(function (User $user) {
                $user->role_name = \optional(\optional($user->roles)->first())->name;
                $user->role = $user->roles->first();
                $user = $user->updateUserHtml();
                return $user;
            }),
            'roles' => $roles,
            'search_placeholder' => $searchPlaceholder,
            'users_count' => $users_count,
            'page_count' => \ceil($max_amount_of_pages),
            'users_count_overall' => User::query()->count(),
            'search' => \request('search')
        ]);
    }

    public function assignRole(Request $request, User $user)
    {

        $role = $request->role ? Role::findById($request->role) : null;
        if (empty($role)) {
            $user->syncRoles([]);
        } else {
            $user->syncRoles([$role]);
        }
        Session::flash('message', 'The role was assigned successfully.');
        Session::flash('alert-class', 'alert-success');
        return \redirect()->route('viewUsers', ['page' => \request('page')]);
    }

    public function update(UserUpdateRequest $request)
    {
        User::find((int) $request->id)->update($request->except(['password']) + ['password' => Hash::make($request->password)]);
        Session::flash('message', 'The user was saved successfully.');
        Session::flash('alert-class', 'alert-success');

        return \redirect()->route('viewUsers', ['page' => \request('page')]);
    }

    public function delete(User $user)
    {
        User::find($user->id)->delete();
        Session::flash('message', 'The user was deleted successfully.');
        Session::flash('alert-class', 'alert-success');
        return \redirect()->back();
    }
}
