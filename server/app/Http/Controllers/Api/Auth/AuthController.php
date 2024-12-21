<?php

namespace App\Http\Controllers\Api\Auth;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Setting;
use App\Models\Node\Node;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Http\Requests\Api\Auth\PasswordEmailRequest;
use App\Http\Requests\Api\Auth\PasswordUpdateRequest;

class AuthController extends Controller
{
    public function register(RegisterRequest $request)
    {
        $token = Str::random(50);

        $setting = getSetting('registration_role');

        $api_email_verification = (bool) \getSetting('api_email_verification') ?? false;


        $email_token = Str::random(50);

        if ($api_email_verification) {
            $this->processVerificationEmail($request->email, $email_token);
        }
        $role = !empty($setting) ? Role::find($setting) : null;
        $user = User::create($request->except('password') + [
            'last_login_at' => Carbon::now(),
            'password' => Hash::make($request->password),
            'password_reset_token' => $token,
            'email_verification_token' => $email_token
        ]);

        if (!empty($role)) {
            $user->assignRole($role);
        }

        $token = $user->createToken($user->name . '_' . Carbon::now(), ['*'], Carbon::now()->addDays(6))->plainTextToken;

        return response()->json(['user' => $user, 'token' => $token]);
    }

    public function sendPasswordEmail(PasswordEmailRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!empty($user)) {
            $this->processPasswordEmail($user->email, $user->password_reset_token);
        }
        return \response()->json(['message' => "An email was sent to the provided email address"]);
    }

    public function resetPassword(PasswordUpdateRequest $request, $param)
    {
        $user = User::where('password_reset_token', $param)->first();
        if (!empty($user)) {
            $email = $user->email;
            $token = Str::random(50);
            $this->updateUser($user, ['password' => Hash::make($request->password), 'password_reset_token' => $token]);
            \defer(fn () =>   $this->sendEmail($email, 'Password Update', 'Your password was updated successfully'));
        }
        return response()->json(['message' => "You've updated your password successfully."]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::query()->whereEmail($request->email)->first();
        $api_email_verification = (bool) \getSetting('api_email_verification') ?? false;
        // no record found
        if (empty($user)) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }
        // password mismatch
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid Credentials'], 401);
        }

        $token = $user->createToken("auth_token" . '_' . Carbon::now(), ['*'], Carbon::now()->addDays(6))->plainTextToken;

        $user = $this->updateUser($user, ['last_login_at' => Carbon::now()]);

        // email verification check
        if ($api_email_verification == false) {
            return  \response()->json(['token' => $token, 'user' => $user]);
        }
        //if empty the user needs to verify email address
        if (empty($user->email_verified_at)) {

            $email_token = Str::random(30);

            $this->processVerificationEmail($user->email, $email_token);

            $this->updateUser($user, ['email_verification_token' => $email_token]);

            return response()
                   ->json([
                    'message' => 'Please verify your email address, an email was sent to your email address when registered.'
                ], 401);
        }

        return \response()->json(['token' => $token, 'user' => $user]);
    }


    public function logout()
    {
        $user = $this->auth_user();
        Cache::forget('auth_user_permissions_'.$user->id);
        Cache::forget('auth_nodes_user_'.$user->id);
        $user->tokens()->delete();

        return response()->json(['message' => 'bye bye..'], 200);

    }

    public function verifyEmail($token)
    {
        $user = User::where('email_verification_token', $token)->first();
        $token = '';
        if (!empty($user)) {
            $user = $this->updateUser($user, ['email_verified_at' => Carbon::now(), 'email_verification_token' => str::random(31)]);
            $token = $user->createToken('auth_token' . '_' . Carbon::now(), ['*'], Carbon::now()->addDays(6))->plainTextToken;
        }
        return \response()->json(['message' => "Your email was successfully verified.", 'token' => $token]);
    }
}
