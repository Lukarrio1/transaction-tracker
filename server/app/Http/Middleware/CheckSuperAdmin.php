<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = request()->user();
        $setting = \optional(Cache::get('settings',\collect(Setting::all()))->where('key', 'admin_role')->first())->getSettingValue();
        $allowed_login_roles = \optional(Cache::get('settings', \collect(Setting::all()))->where('key', 'allowed_login_roles')->first())->getSettingValue('last') ?? \collect([]);
        $role = !empty($setting) ? Role::find((int)$setting) : null;
        // Check if the user is authenticated and has the "Super Admin" role
        if (
            !empty($role) && $user->hasRole($role) ||
            !empty(\auth()->user()) &&
            \in_array(auth()->user()->roles->pluck('id')->first(), $allowed_login_roles->toArray())
        ) {


            return $next($request);
        }
        Auth::logout();
        return redirect()->route('login');
    }
}
