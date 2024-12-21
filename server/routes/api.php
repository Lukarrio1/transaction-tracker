<?php

use App\Models\User;
use App\Models\Setting;
use App\Models\Node\Node;
use App\Models\Tenant\Tenant;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Config;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\CompressResponse;

// Ensure routes are cache
// Retrieve cached routes
$routes = Cache::get('routes', collect([]));
$routes->each(function ($route) {
    $properties = $route->properties['value'];
    if (empty($properties->route_method) || empty($properties->node_route) || empty($properties->route_function)) {
        return;
    }
    $method = strtolower($properties->route_method);

    $node_route = \collect(\explode('/', $properties->node_route))
        ->filter(function ($dt, $key) use ($properties) {
            if (array_search('api', \explode('/', $properties->node_route)) < $key) {
                return true;
            }
            return false;
        })->join('/');

    $routeFunctionParts = explode('::', $properties->route_function);
    if (count($routeFunctionParts) !== 2) {
        return;
    }

    list($controller, $methodName) = $routeFunctionParts;


    Route::$method($node_route, [$controller, $methodName])
        ->middleware([
            AuthMiddleware::class,
           CompressResponse::class
        ]);
});
