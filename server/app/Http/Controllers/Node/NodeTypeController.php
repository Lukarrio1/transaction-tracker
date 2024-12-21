<?php

namespace App\Http\Controllers\Node;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NodeTypeController extends Controller
{
    public function  index (){
        return \view('Nodes.Node_Types.View');
    }
}
