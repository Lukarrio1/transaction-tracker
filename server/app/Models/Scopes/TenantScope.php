<?php

namespace App\Models\Scopes;

use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Builder;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {

        $tenantId = (int) Cache::get('tenant_id');
        $multi_tenancy = (int)optional(collect(Cache::get('settings'))
            ->where('key', 'multi_tenancy')->first())
            ->getSettingValue('first');
        $setting
            = (int)optional(collect(Cache::get('settings'))
                ->where('key', 'admin_role')->first())
                ->getSettingValue();
        $role_for_checking = !empty($setting) ? Role::find((int)$setting) : null;
        if ($multi_tenancy == 1 && !empty(\auth()->user())) {
            $builder->where('tenant_id', \auth()->user()->hasRole($role_for_checking) ? null : $tenantId);
        }
    }
}
