<?php

namespace App\Models\Tenant;

use App\Models\User;
use App\TenantTrait;
use App\Models\Setting;
use App\Models\TenantUser;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends Model
{

    use HasFactory;
    use TenantTrait;

    protected $guarded = ['id'];
    protected $appends = ['api_base_url'];

    public function getStatusAttribute($value)
    {
        return ['value' => $value, 'human_value' => [0 => "In Active", 1 => "Active"][$value]];
    }


    public function getApiBaseUrlAttribute()
    {
        $base_link = optional(collect(Cache::get('settings'))
            ->where('key', 'app_url')->first())->properties . "/api/tenant/" .$this->id;
        // ->where('key', 'app_url')->first())->properties . "/{" . collect(\explode(' ', $this->name))->map(fn ($word) => \strtolower($word))->join('_')."}";
        return $base_link;
    }


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id', 'id');
    }


    public function addTenantIdToCurrentItem($id)
    {
        $multi_tenancy = (int) \optional(Setting::where('key', 'multi_tenancy')->first())->getSettingValue('first');
        return  $multi_tenancy == 1 ? ['tenant_id' => $id] : ['tenant_id' => null];
    }
}
