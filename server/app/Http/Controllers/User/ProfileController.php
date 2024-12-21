<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\User\UserUpdateRequest;

class ProfileController extends Controller
{

    public function update(UserUpdateRequest $request,$user)
    {
        User::find((int)$request->id)
        ->update($request->except(['password']+['password'=>Hash::make($request->password)]));

        return \redirect()->route('viewUsers');
    }
}
