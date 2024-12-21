<?php

namespace App\Http\Controllers\Api;

use App\Models\Export;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class SettingController extends Controller
{

    public function settings()
    {
        $settings = [];
        if (!Cache::has('api_settings')) {
            $settings =
                Setting::select('key', 'properties', 'id', 'allowed_for_api_use')
                ->get()
                ->map(function ($item) {
                    $item->properties = $item->allowed_for_api_use == 1 ?
                        ['key' => $item->getSettingValue('first'), 'value' => $item->getSettingValue('last')] :
                        ['key' => '', 'value' => null];
                    unset($item->allowed_for_api_use);
                    unset($item->id);
                    return $item;
                });
            Cache::set('api_settings', $settings, $this->cache_ttl);
        } else {
            $settings = Cache::get('api_settings');
        }
        return \response()->json(['settings' => [...$settings->toArray(), [
            'key' => "is_cache_valid",
            "properties" => ["key" => "is_cache_valid", "value" => Cache::get("is_cache_valid")]
        ]]]);
    }
}
