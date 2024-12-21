<?php

namespace App\Models;

use App\Models\BaseModel;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Redirect extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'role_id',
        'redirect_to_after_login',
        'redirect_to_after_register',
        'redirect_to_after_logout',
        'redirect_to_after_password_reset'
    ];


    public function role()
    {
        return $this->hasOne(Role::class, 'id', 'role_id');
    }
}
