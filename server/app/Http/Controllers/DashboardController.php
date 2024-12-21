<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Audit;
use App\Models\Node\Node;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $take = 10;
        $users = User::query()->latest();
        $nodes = Node::query()->orderBy('updated_at', 'desc');
        $audits = Audit::query()->latest('created_at');
        return \view('Dashboard.View', [
            'new_users' => $users->take($take)->get(),
            'last_used_routes' => $nodes->where('node_type', 1)->take($take)->get(),
            'audit_history' => $audits->take($take)->get()
        ]);
    }

}
