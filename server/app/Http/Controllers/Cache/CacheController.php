<?php

namespace App\Http\Controllers\Cache;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Session;

class CacheController extends Controller
{
    public $cacheOptions = [
        "clear_system_cache" => "cache:clear",
        'clear_views_cache' => 'view:clear',
        "reload_cache" => 'optimize',
        // "autoload_classes" => ''
    ];

    public function __construct()
    {
        $this->middleware('can:can clear cache');
    }

    public function index()
    {

        return view('Cache.View', ['cacheOptions' => $this->cacheOptions]);
    }

    public function clearCache()
    {
        $cache_to_clear = \collect(\request()->all())->keys();
        if (
            \collect($this->cacheOptions)->keys()
            ->filter(fn ($key) => in_array($key, $cache_to_clear->toArray()))->count()
            == \count($this->cacheOptions)
        ) {
            $cache_to_clear->each(fn ($key) => Artisan::call($this->cacheOptions[$key]));
        } else {
            Artisan::call('cache:clear');
            Artisan::call('optimize:clear');
        }
        Cache::set("is_cache_valid", Str::random(40));
        Session::flash('message', 'The system cache was refreshed successfully.');
        Session::flash('alert-class', 'alert-success');

        return \redirect()->back();
    }
}
