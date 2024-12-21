<?php

namespace App\Http\Controllers\Api;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class TestController extends Controller
{
    public function saveTodo(Request $request)
    {
        $todos = Cache::has('todos') ? \collect(Cache::get('todos')) : \collect([]);
        $currentTodo = $todos->firstWhere('id', $request->id);

        if (!empty($currentTodo)) {
            $todos = $todos->map(function ($todo) use ($request) {
                if ($todo['id'] == $request->id) {
                    $todo['todo'] = $request->todo;
                }
                return $todo;
            });
            // dd($todos);
        } else {
            $todos->push(['id' => Str::random(4), 'todo' => $request->todo]);
        }
        Cache::set('todos', $todos->values());

        return ['todos' => Cache::get('todos')];
    }

    public function todos()
    {
        $todos = Cache::get('todos', []);

        return ['todos' => $todos];
    }

    public function removeTodo()
    {
        Cache::forget('todos');
        return \response()->json(['message' => 'todo deleted']);
    }
}
