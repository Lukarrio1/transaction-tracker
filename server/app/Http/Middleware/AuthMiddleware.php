<?php

namespace App\Http\Middleware;

use Closure;
use PSpell\Config;
use App\Models\Audit;
use App\Models\Setting;
use App\Models\Node\Node;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Laravel\Sanctum\PersonalAccessToken;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // return $next($request);
        // Force response content to be JSON

        $request->headers->set('Accept', 'application/json');

        // Extract the token from the request
        $token = $request->bearerToken();
        // dd(\request()->tenant);
        // Determine the current route's function name
        $currentRoute = join('::', explode('@', Route::currentRouteAction()));

        Cache::set('tenant_id', Route::current()->parameter('tenant'));
        $currentRouteNode = null;
        // Retrieve the route node from the cache
        if (Cache::has('routes')) {
            $currentRouteNode = Cache::get('routes')
                ->where('properties.value.route_function', $currentRoute)
                ->first();
        } else {
            (new Controller())->clearCache();
            $currentRouteNode = Cache::get('routes')
                ->where('properties.value.route_function', $currentRoute)
                ->first();
        }

        if (empty($currentRouteNode)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $this->updateNode($currentRouteNode->id);
        // Check the required authentication level
        $authLevel = $currentRouteNode->authentication_level['value'];
        if (in_array($authLevel, [0, 2])) {
            return $this->handleAuthLevelZeroOrTwo($request, $next, $token, $authLevel);
        } else {
            return $this->handleAuthLevelOne($request, $next, $token, $currentRouteNode);
        }
    }

    /**
     * Handle routes with authentication level 0 or 2.
     */
    protected function handleAuthLevelZeroOrTwo(Request $request, Closure $next, $token, $authLevel)
    {
        $personalAccessToken = PersonalAccessToken::findToken($token);

        if ($authLevel == 0 && $personalAccessToken && $personalAccessToken->tokenable instanceof \App\Models\User) {
            Auth::setUser($personalAccessToken->tokenable);
            if (request()->user()) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return $next($request);
    }

    /**
     * Handle routes with authentication level 1.
     */
    protected function handleAuthLevelOne(Request $request, Closure $next, $token, $currentRouteNode)
    {
        $app_auditing = (int) getSetting('app_auditing');

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $personalAccessToken = PersonalAccessToken::findToken($token);
        if ($personalAccessToken && $personalAccessToken->tokenable instanceof \App\Models\User) {
            Auth::setUser($personalAccessToken->tokenable);
            $node_audit_message = empty($currentRouteNode) ? '' : \optional(\optional($currentRouteNode)->properties['value'])->node_audit_message;
            if ($app_auditing == 1) {
                Audit::create(
                    [
                        'user_id' => \request()->user()->id,
                        'node_id' => $currentRouteNode->id,
                        'message' => (new Audit())->setUpMessage($node_audit_message),
                    ]
                );
            }
            // Check admin role
            $adminRoleId = \getSetting('admin_role');
            $adminRole = $adminRoleId ? Role::find($adminRoleId) : null;

            if ($adminRole && request()->user()->hasRole(optional($adminRole)->name)) {
                return $next($request);
            }

            // Check route-specific permissions
            if (
                !empty($currentRouteNode->permission) &&
                request()->user()->hasPermissionTo(optional($currentRouteNode->permission)->name)
            ) {
                return $next($request);
            }

            // If no specific permissions are required, allow access
            if (empty($currentRouteNode->permission)) {
                return $next($request);
            }

            return response()->json(['error' => 'Forbidden'], 403);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    protected function updateNode($id)
    {
        // Node::find($id)->touch();
    }
}
