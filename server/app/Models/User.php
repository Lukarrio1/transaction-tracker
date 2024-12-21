<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use PSpell\Config;
use App\TenantTrait;
use App\TracksUserLogin;
use App\HasCustomPagination;
use App\Models\Tenant\Tenant;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Scopes\TenantScope;
use App\Models\Reference\Reference;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Traits\HasRoles;
use App\Mail\Api\Auth\EmailVerification;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use HasRoles;
    use HasApiTokens;
    use HasCustomPagination;
    // use Encryptable;
    use SoftDeletes;
    use TenantTrait;
    use TracksUserLogin;

    protected $table = "users";
    public $table_name;
    public function __construct()
    {
        $this->table_name = $this->table;
        static::addGlobalScope('UserDeleteAt', function (Builder $builder) {
            $builder->whereNull('deleted_at');
        });
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'password_reset_token',
        'tenant_id',
        'last_login_at',
        'email_verification_token'
    ];
    // protected $encryptable = ['name','email'];

    public function updateFieldTemplate()
    {
        return [
            'id' => "<input type='hidden' value='" . $this->id . "' name='id'></input>",
            'name' => "<div class='mb-3'>
                    <label for='name' class='form-label'>Name</label>
                    <input
                    type='text'
                    class='form-control'
                    id='name'
                     aria-describedby='name'
                     name='name'
                     value='" . $this->name . "'>
                </div>",
            'email' => "<div class='mb-3'>
                    <label for='name' class='form-label'>Email</label>
                    <input
                    type='text'
                    class='form-control'
                    id='email'
                     aria-describedby='email'
                     name='email'
                     value='" . optional($this)->email . "'>
                </div>",
            'password' => "<div class='mb-3'>
                    <label for='name' class='form-label'>Password</label>
                    <input
                    type='password'
                    class='form-control'
                    id='password'
                     aria-describedby='password'
                     name='password'
                     value=''>
                </div>",
            'confirm_password' => "<div class='mb-3'>
                    <label for='name' class='form-label'>Confirm Password</label>
                    <input
                    type='password'
                    class='form-control'
                     id='confirm_password'
                     aria-describedby='confirm_password'
                     name='confirm_password'
                     value=''>
                </div>",
        ];
    }
    public function references()
    {
        return $this->hasMany(Reference::class, 'owner_id', 'id');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime'
        ];
    }

    public function updateUserHtml()
    {
        $this->updateHtml = collect($this->updateFieldTemplate())
            ->filter(function ($field, $key) {
                if (\in_array($key, ['password', 'confirm_password'])) {
                    return auth()->user()->hasPermissionTo("can edit users password");
                }
                return true;
            })->join(' ');
        return $this;
    }

    public function land()
    {
        return $this->hasOne(Tenant::class, 'owner_id', 'id');
    }

    public function deleteInactiveUsers()
    {
        $delete_inactive_users_after = (int) getSetting('delete_inactive_users');
        if ($delete_inactive_users_after == 0) {
            return false;
        }
        $date_for_deletion = Carbon::now();
        $users = User::select('id', 'last_login_at', 'name')
            ->get()
            ->filter(function ($user) use ($date_for_deletion, $delete_inactive_users_after) {
                return !empty($user->last_login_at) &&
                    Carbon::parse(Carbon::parse($user->last_login_at)->toDateString())->diffInMonths($date_for_deletion) >= $delete_inactive_users_after;
            });
        User::whereIn('id', $users->pluck('id'))->delete();
    }

    public function sendVerificationEmail()
    {
        if ($this->email_verified_at == null) {
            Mail::to($this->email)->send(new EmailVerification());
        }
    }
}
