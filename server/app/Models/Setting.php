<?php

namespace App\Models;

use App\TenantTrait;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;
    use TenantTrait;
    protected $guarded = ['id'];
    public function __construct()
    {
        // $this->initializeTenancy();
    }

    public const ANIMATIONS = [
        // 'w3-animate-fading' => 'w3-animate-fading',
        'w3-animate-zoom' => 'w3-animate-zoom',
        'w3-animate-opacity' => 'w3-animate-opacity',
        'w3-animate-right' => 'w3-animate-right',
        'w3-animate-left' => 'w3-animate-left',
        'w3-animate-bottom' => 'w3-animate-bottom',
        'w3-animate-top' =>  'w3-animate-top'
    ];

    public function SETTING_OPTIONS($key, $value, $setting_key, $field_value)
    {
        $html = '';
        $prev_val = '';
        $field_value = $key == "multi_select" ? $field_value : \optional(\optional($field_value->where('key', $setting_key))->first())->properties;
        $value = !empty($prev_val) ? $prev_val : collect($value);
        switch ($key) {
            case 'drop_down':
                $value->each(function ($key, $val) use (&$html, $field_value) {
                    $selected = $field_value == $val . "_" . $key ? "selected" : '';
                    $html .= "<option value='" . $val . "_" . $key . "' $selected>$val</option>";
                });
                $html = "<select class='form-select' name='value'>$html</select>";
                break;
            case 'multi_select':
                $value->each(function ($key, $val) use (&$html, $field_value) {
                    $selected = \in_array($key, \collect($field_value)->toArray()) ? "selected" : '';
                    $html .= "<option value='" . $val . "_" . $key . "' $selected>$val</option>";
                });
                $html = "<select class='form-select' name='value[]' multiple>$html</select>";
                break;
            case 'input':
                $html = "<input class='form-control' name='value' value='" . $field_value . "'>";
                break;
            case 'input_number':
                $html = "<input class='form-control' type='number' placeholder='How many (number) ?' name='value' value='" . $field_value . "'>";
                break;
            case 'input_email':
                $html = "<input class='form-control' type='email' name='value' value='" . $field_value . "'>";
                break;
            case 'magic_string':
                $html = "eg: profile_image_0|bio_0|teachers_1
                        <textarea class='form-control' name='value'>$field_value</textarea>
                  ";
                break;
            case "config_string":
                $html = "eg: example_db:DB_CONNECTION=mysql|
                             DB_HOST=127.0.0.1|
                             DB_PORT=3306|
                             DB_DATABASE=laravel_reimagined|
                             DB_USERNAME=root|
                             DB_PASSWORD=root_user,
                        <textarea class='form-control' name='value'>$field_value</textarea>
                  ";
                // no break
            default:
                # code...
                break;
        }

        return $html;
    }

    public function SETTING_KEYS($key, $field_value)
    {
        $roles = Cache::get('roles');
        $keys = collect([
            'admin_role' => [
                'field' => $this->SETTING_OPTIONS('drop_down', $roles, $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'registration_role' => [
                'field' => $this->SETTING_OPTIONS('drop_down', $roles, $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'app_name' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'client_app_name' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_MAILER') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_HOST') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_PORT') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_USERNAME') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_PASSWORD') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_ENCRYPTION') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_FROM_ADDRESS') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            \strtolower('MAIL_FROM_NAME') => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'multi_tenancy' => [
                'field' => $this->SETTING_OPTIONS('drop_down', [true => 'true', false => 'false'], $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'first'],
            ],
            'multi_tenancy_role' => [
                'field' => $this->SETTING_OPTIONS('drop_down', $roles, $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'mail_url' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'app_url' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'client_app_url' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'app_version' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'app_animation' => [
                'field' => $this->SETTING_OPTIONS('drop_down', self::ANIMATIONS, $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'app_auditing' => [
                'field' => $this->SETTING_OPTIONS('drop_down', ['true' => true, 'false' => false], $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'allowed_login_roles' => [
                'field' => $this->SETTING_OPTIONS('multi_select', $roles, $key, Cache::get('setting_allowed_login_roles', [])),
                'handle' => ['action' => 'multi_split', 'value' => 'last'],
            ],
            'delete_inactive_users' => [
                'field' => $this->SETTING_OPTIONS('input_number', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'brotli_compression_ratio' => [
                'field' => $this->SETTING_OPTIONS('input_number', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'gzip_compression_ratio' => [
                'field' => $this->SETTING_OPTIONS('input_number', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'site_email_address' => [
                'field' => $this->SETTING_OPTIONS('input_email', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'redirect_to_after_login' => [
                'field' => $this->SETTING_OPTIONS('drop_down', Cache::get('redirect_to_options', []), $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'redirect_to_after_register' => [
                'field' => $this->SETTING_OPTIONS('drop_down', Cache::get('redirect_to_options', []), $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'redirect_to_after_logout' => [
                'field' => $this->SETTING_OPTIONS('drop_down', Cache::get('redirect_to_options', []), $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'cache_driver' => [
                'field' => $this->SETTING_OPTIONS('drop_down', ['File Storage' => 'file', 'Database Storage' => 'database', 'Redis Storage' => 'redis.cache'], $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'api_email_verification' => [
                'field' => $this->SETTING_OPTIONS('drop_down', ['Enabled' => true, 'Disabled' => false], $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'reference_types' => [
                'field' => $this->SETTING_OPTIONS('magic_string', [], $key, $field_value),
                'handle' => ['action' => 'magic_split', 'value' => 'last'],
            ],
            'database_configuration' => [
                'field' => $this->SETTING_OPTIONS('config_string', [], $key, $field_value),
                'handle' => ['action' => 'config_split', 'value' => 'last'],
            ],
            'database_backup' => [
                'field' => $this->SETTING_OPTIONS('drop_down', ['Enabled' => true, 'Disabled' => false], $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
            'database_backup_configuration' => [
                'field' => $this->SETTING_OPTIONS('multi_select', Cache::get('setting_databases'), $key, Cache::get('setting_backup_databases', [])),
                'handle' => ['action' => 'multi_split', 'value' => 'last'],
            ],
            'cache_ttl' => [
                'field' => $this->SETTING_OPTIONS('input_number', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'search_skip_word' => [
                'field' => $this->SETTING_OPTIONS('input', '', $key, $field_value),
                'handle' => ['action' => '', 'value' => ''],
            ],
            'data_interoperability' => [
                'field' => $this->SETTING_OPTIONS('drop_down', ['Enabled' => true, 'Disabled' => false], $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
              'redirect_to_after_password_reset' => [
                'field' => $this->SETTING_OPTIONS('drop_down', Cache::get('redirect_to_options', []), $key, $field_value),
                'handle' => ['action' => 'split', 'value' => 'last'],
            ],
        ]);
        return $keys->get($key);
    }

    public function getSettingValue($value = '', $internal_use = true)
    {

        $key = $this->SETTING_KEYS($this->key, optional(collect(Cache::get('settings'))))['handle'];
        switch ($key['action']) {
            case 'split':
                $value = !empty($value) ? $value : $key['value'];
                $value = \explode('_', $this->properties)[$value == 'last' ? count(explode('_', $this->properties)) - 1 : 0];
                break;
            case 'multi_split':
                $value = !empty($value) ? $value : $key['value'];
                $value = $value == 'first' ? "<ul class='list-group list-group-flush'>" . collect(\explode('|', $this->properties))
                    ->map(fn ($item) => \collect(\explode('_', $item))
                        ->filter(fn ($item, $idx) =>  $idx == 0)
                        ->map(fn ($item) => \collect(\explode('--', $item))->join(' ')))
                    ->flatten()->map(fn ($item) => "<li class='list-group-item'>" . $item . "</li>")->join('') . "</ul>" :
                    collect(\explode('|', $this->properties))->map(fn ($item) => \collect(\explode('_', $item))
                        ->filter(fn ($item, $idx) =>  $idx > 0))->flatten();
                break;
            case "magic_split":
                $value = !empty($value) ? $value : $key['value'];
                $value = $value == "first" ? "<ul class='list-group list-group-flush'>" . collect(\explode('|', $this->properties))
                    ->map(fn ($val) => "<li class='list-group-item'>" . $val . "</li>")->join('') . "</ul>" : \explode('|', $this->properties);
                break;
            case "config_split":
                $value = !empty($value) ? $value : $key['value'];
                $else_val
                    = collect(explode(',', $this->properties))
                    ->mapWithKeys(function ($item) {
                        $itemParts = explode(':', $item);
                        $outerKey = str_replace(["\r", "\n"], '', trim($itemParts[0]));
                        $innerParts = isset($itemParts[1]) ? $itemParts[1] : '';
                        $innerArray = collect(explode('|', $innerParts))->mapWithKeys(function ($val) {
                            $keyValue = explode('=', $val);
                            return [trim($keyValue[0]) => isset($keyValue[1]) ? $keyValue[1] : ''];
                        });
                        return [$outerKey => $innerArray];
                    });
                $value = $value == "first" ? "<ul class='list-group list-group-flush'>" . collect(\explode(',', $this->properties))
                    ->map(fn ($val) => "<li class='list-group-item'>" . $val . "</li>")->join('') . "</ul>" :
                    $else_val;
                break;
            default:
                $value = $this->properties;
                break;
        }
        return $value;
    }

    public function getAllSettingKeys($key = "")
    {
        $keys = \collect([
            'admin_role' => "Super Admin Role",
            'registration_role' => 'Api Registration Role',
            'allowed_login_roles' => "Roles that are allowed to login",
            'app_name' => 'Application Name',
            'client_app_name' => 'Client Application Name',
            'app_url' => 'Server Side URL',
            'client_app_url' => 'Client Side URl',
            'app_version' => 'Application Version',
            'cache_driver' => 'Cache Driver (avoid changing this)',
            "site_email_address" => "Site Email Address",
            'app_animation' => 'Application Animation',
            "api_email_verification" => "Api User Email Verification",
            // 'multi_tenancy' => 'Api Multi Tenancy',
            // "multi_tenancy_role" => "Api Multi Tenancy Role",
            "app_auditing" => "Application Auditing",
            "redirect_to_after_login" => "React router redirect to after login",
            "redirect_to_after_register" => "React router redirect to after register",
            "redirect_to_after_logout" => "React router redirect to after logout",
            "delete_inactive_users" => "Delete Inactive Users after some (months)",
            \strtolower('MAIL_MAILER') => 'Mail Mailer',
            \strtolower('MAIL_HOST') => 'Mail Host',
            \strtolower('MAIL_PORT') => 'Mail Port',
            \strtolower('MAIL_USERNAME') => 'Mail Username',
            \strtolower('MAIL_PASSWORD') => 'Mail Password',
            \strtolower('MAIL_ENCRYPTION') => 'Mail Encryption',
            \strtolower('MAIL_FROM_ADDRESS') => 'Mail Form Address',
            \strtolower('MAIL_FROM_NAME') => 'Mail From Name',
            'mail_url' => "Mail Url",
            'reference_types' => "Reference Types",
            'database_configuration' => "Database Configurations",
            "database_backup_configuration" => "Database Backup Configurations",
            "database_backup" => "Database Backup (Weekly)",
            "cache_ttl"    =>   "Cache Time To Live (seconds)",
            "search_skip_word"    => "Search Skip Word (used to preserve a data interoperability route if the value of a parameter is empty when searching or filtering data)",
            "data_interoperability" => "Data Interoperability",
            'redirect_to_after_password_reset' => "React router redirect to after password reset",
            'brotli_compression_ratio' => "Brotli compression ration from 1 to 11",
            'gzip_compression_ratio' => "Gzip compression ration from 1 to 9",
        ]);
        // ->when($multi_tenancy == 0, function ($collection) {
        //     return $collection->filter((function ($item, $key) {
        //         return $key != "multi_tenancy_role";
        //     }));
        // });
        return $key ? $keys->get($key) : $keys->toArray();
    }
}
