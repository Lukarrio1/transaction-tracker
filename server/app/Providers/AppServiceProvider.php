<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Setting;
use App\Models\Node\Node;
use App\Models\Tenant\Tenant;
use App\Models\ReferenceConfig;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Models\Reference\Reference;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use Illuminate\Container\Attributes\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment('local')) {
            $this->app->register(\Laravel\Telescope\TelescopeServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $cache_driver = Setting::where('key', 'cache_driver')->first();
        if (!empty($cache_driver)) {
            $cache_driver = $cache_driver->getSettingValue('last');
        } else {
            $cache_driver = "file";
        }
        Config::set('cache.default', $cache_driver);

        if (!Cache::has('settings')) {
            Cache::add('settings', Setting::all());
        }

        if (!Cache::has('redirect_to_options')) {
            $links = Node::query()
                    ->enabled()
                    ->links()
                    ->get()
                    ->map(function ($item) {
                        $temp = \collect([]);
                        $temp->put('name', $item->name);
                        $temp->put('route', $item->properties['value']->node_route);
                        return $temp->toArray();
                    })
                    ->pluck('route', 'name');
            Cache::add('redirect_to_options', $links);
        }

        if (!Cache::has('role_base_redirects')) {
            $links = Node::query()
                    ->enabled()
                    ->links()
                    ->get()
                    ->map(function (Node $item) {
                        $temp = \collect([]);
                        $temp->put('name', $item->name);
                        $temp->put('route', $item->properties['value']->node_route);
                        $temp->put('uuid', $item->uuid);
                        return $temp;
                    });
            Cache::add('role_base_redirects', $links);
        }

        $mail_config = [
            \strtolower('MAIL_HOST') => \getSetting('mail_host'),
            \strtolower('MAIL_PORT') => \getSetting('mail_port'),
            \strtolower('MAIL_USERNAME') => \getSetting('mail_username'),
            \strtolower('MAIL_PASSWORD') => \getSetting('mail_password'),
            \strtolower('MAIL_ENCRYPTION') => \getSetting('mail_encryption'),
            \strtolower('MAIL_FROM_ADDRESS') => \getSetting('mail_from_address'),
            \strtolower('MAIL_FROM_NAME') => \getSetting('mail_from_name'),
            'mail_url' => \getSetting('mail_url'),
            'transport' => \getSetting('mail_mailer')
        ];
        Config::set('mail', $mail_config);

        if (!Cache::has('roles')) {
            Cache::set('roles', Role::all()->pluck('id', 'name'));
        }

        if (!Cache::has('setting_allowed_login_roles')) {
            $allowed_login_roles = \getSetting("allowed_login_roles") ?? \collect([]);
            Cache::add('setting_allowed_login_roles', $allowed_login_roles->toArray());
        }

        if (!Cache::has('setting_databases')) {
            $databases = collect([]);
            $data_configurations = \getSetting('database_configuration') ?? [];
            collect($data_configurations)
                    ->keys()
                    ->each(fn ($db) => $databases->put($db, $db));
            Cache::add('setting_databases', $databases);
        }

        if (!Cache::has('setting_backup_databases')) {
            $item = \getSetting('database_backup_configuration') ?? [];
            Cache::add('setting_backup_databases', $item);
        }

        if (!Cache::has('routes')) {
            $nodes = Node::enabled()
                    ->routes()
                    ->get();
            Cache::add('routes', $nodes);
        }

        if (!Cache::has('references')) {
            Cache::add('references', ReferenceConfig::query()
                            ->whereIn('type', \getSetting('reference_types') ?? collect([]))
                            ->distinct('type')
                            ->get());
        }

        \collect(\getSetting('reference_types'))
                ->each(function ($ref) {
                    $rel_type = collect(\explode('_', $ref));
                    $ref = Cache::get('references')->where('type', $ref)->first();
                    if ($rel_type->count() > 1 && !empty($ref)) {
                        $owned_model = $ref->owned_model;
                        $owner_model = $ref->owner_model;
                        $has_many = (int) $rel_type->last() == 1 ? "hasManyThrough" : "hasOneThrough";

                        // creates the owner relationships
                        $owner_model::resolveRelationUsing($rel_type->first(), function ($owner_model) use ($owned_model, $has_many, $ref) {
                            return $owner_model->$has_many($owned_model, Reference::class, 'owner_id', 'id', 'id', 'owned_id')
                            ->where('references.type', $ref->type);
                        });
                        // creates the reverse of the owned relationship
                        $owned_model::resolveRelationUsing($rel_type->first() . "_owner", function ($owned_model) use ($owner_model, $has_many, $ref) {
                            return $owned_model->hasOneThrough($owner_model, Reference::class, 'owned_id', 'id', 'id', 'owner_id')
                            ->where('references.type', $ref->type);
                        });
                    }
                });

        \collect(
            \getSetting('database_configuration')
        )->filter(function ($item) {
            return !empty($item->get('DB_CONNECTION') ?? '');
        })
                ->each(function ($item, $key) {
                    try {
                        Config::set("database.connections.{$key}", [
                            'driver' => $item->get('DB_CONNECTION') ?? "mysql",
                            'host' => $item->get('DB_HOST'),
                            'port' => $item->get('DB_PORT'),
                            'database' => $item->get('DB_DATABASE'),
                            'username' => $item->get('DB_USERNAME'),
                            'password' => $item->get('DB_PASSWORD'),
                            'charset' => 'utf8',
                            'collation' => 'utf8_unicode_ci',
                            'prefix' => '',
                        ]);
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                });
        (new User())->deleteInactiveUsers();
    }

    // public function boot(){
    // }
}
