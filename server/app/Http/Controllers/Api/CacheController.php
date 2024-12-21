<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class CacheController extends Controller
{
    public function monitor(): JsonResponse
    {
        return \response()->json(['is_cache_valid' => Cache::get('is_cache_valid')]);
    }
}
