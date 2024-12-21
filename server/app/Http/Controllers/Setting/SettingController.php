<?php

namespace App\Http\Controllers\Setting;

use PSpell\Config;
use App\Models\Setting;
use App\Models\Node\Node;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Tenant\Tenant;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Cache\CacheController;

class SettingController extends Controller
{
    public $tenancy;
    public function __construct()
    {
        $this->middleware('can:can crud settings');
        $this->tenancy = new Tenant();
    }

    public function index($setting_key = 'admin_role')
    {
        $setting = new Setting();
        $search = Str::lower(\trim(request()->get('search')));
        $settings_for_display
            = $setting->query()->latest('updated_at');
        $settings_count = $settings_for_display
            ->count();
        $settings_for_display = $settings_for_display->when($search, fn ($q) => $q->where('key', 'LIKE', '%' . $search . '%'))->get();
        $keys
            = collect($setting->getAllSettingKeys())
            ->filter(fn ($key, $idx) => \request()->get('setting_key') == $idx || !\in_array($idx, $settings_for_display->pluck('key')->toArray()));

        $setting_key = empty(\request()->get('setting_key')) ? $keys->keys()->first() : \request()->get('setting_key');
        $field_value = optional(collect(Cache::get('settings')));

        return view('Setting.View', [
            'keys' => $keys,
            'key_value' => $setting->SETTING_KEYS($setting_key, $field_value)['field'] ?? '',
            'allowed_for_api_use' => \collect(Cache::get('settings', \collect(Setting::all()))
                ->firstWhere('key', $setting_key))->get('allowed_for_api_use', 0),
            'setting_key' => $setting_key,
            'settings' => [...$settings_for_display],
            'settings_count' => $settings_count
        ]);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), ['value' => ['required'], 'setting_key' => ['required']]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        $value =
        \in_array($request->setting_key, ["allowed_login_roles", 'not_exportable_tables', 'database_backup_configuration']) ?
        \collect($request->value)->map(function ($item) {
            return \collect(\explode(' ', $item))->join("--");
        })->join('|') : $request->value;

        Setting::updateOrCreate(
            [
                'key' => $request->setting_key
            ],
            $request->merge(['properties' => $value])->all()
        );
        Cache::forget('settings');
        Cache::forget('setting_allowed_login_roles');
        Cache::forget('setting_backup_databases');
        Session::flash('message', 'The setting value was saved successfully.');
        Session::flash('alert-class', 'alert-success');
        \defer(fn () => (new CacheController())->clearCache());

        return \redirect()->route('viewSettings');
    }

    public function delete($setting_key)
    {

        $setting = Setting::where('key', $setting_key)->first();
        $setting->delete();
        return \redirect()->route('viewSettings');
    }
}
