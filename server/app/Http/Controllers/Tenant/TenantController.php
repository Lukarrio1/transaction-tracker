<?php

namespace App\Http\Controllers\Tenant;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Tenant\Tenant;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TenantController extends Controller
{

    public function __construct()
    {
        $this->middleware('can:can crud tenant');
    }

    public function index($Tenant = null)
    {
        $tenants = Tenant::query();
        return \view('Multi_Tenancy.View', [
            'tenant' => Tenant::with('owner')->find($Tenant),
            'tenants' => $tenants->get(),
            'users' => User::all()
        ]);
    }

    public function save(Request $request)
    {

        $rules = [
            'name' => ['required', 'min:3'],
            'email' => ['required', 'email', 'required'],
            'description' => ['required', 'min:5'],
            'status' => ['required'],
            'owner_id' => ['required']
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return \redirect()->back()->withErrors($validator)->withInput();
        }
        $tenant = Tenant::updateOrCreate(['id' => $request->id], $request->all());

        $user =  User::find((int)$request->owner_id);
        $user->update(['tenant_id' => $tenant->id]);
        return \redirect()->route('viewTenants');
    }

    public function delete(Tenant $tenant)
    {
        $tenant->delete();
        return \redirect()->route('viewTenants');
    }
}
