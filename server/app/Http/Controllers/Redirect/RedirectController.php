<?php

namespace App\Http\Controllers\Redirect;

use App\Models\Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Redirects\SaveRequest;

class RedirectController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:can crud redirects');
    }

    public function index($redirect_edit = null)
    {
        $links =  Cache::get('role_base_redirects', collect([]));
        $roles = Cache::get('roles', []);
        $redirect_configs = Redirect::with('role:name,id')
        ->get()
        ->map(function ($item) use ($links) {
            $links =  collect($links, []);
            $item->redirect_to_after_login_name = $links->firstWhere('uuid', $item->redirect_to_after_login)?->get('name');
            $item->redirect_to_after_register_name = $links->firstWhere('uuid', $item->redirect_to_after_register)?->get('name');
            $item->redirect_to_after_logout_name = $links->firstWhere('uuid', $item->redirect_to_after_logout)?->get('name');
            $item->redirect_to_after_password_reset_name = $links->firstWhere('uuid', $item->redirect_to_after_password_reset)?->get('name');
            return $item;
        });
        return view('Redirects.View', ['links' => $links,'roles' => $roles,'redirects' => $redirect_configs,'redirect_edit' => $redirect_edit]);
    }

    public function save(SaveRequest $request)
    {
        Redirect::updateOrCreate(['role_id' => $request->role_id], $request->validated());
        defer(fn () => $this->clearCache());
        Session::flash('message', 'The role base redirect config was saved successfully.');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route('roleRedirects');
    }

    public function edit(Redirect $redirect)
    {
        return $this->index($redirect);
    }

    public function delete(Redirect $redirect)
    {
        $redirect->delete();
        defer(fn () => $this->clearCache());
        return redirect()->route('roleRedirects');
    }

}
