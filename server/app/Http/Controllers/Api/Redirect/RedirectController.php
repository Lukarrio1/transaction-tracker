<?php

namespace App\Http\Controllers\Api\Redirect;

use App\Models\Redirect;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RedirectController extends Controller
{
    public function clientRedirects()
    {
        $redirects = Redirect::all();
        return response()->json(['redirects' => $redirects]);
    }
}
