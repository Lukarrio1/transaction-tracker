<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Audit extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function setUpMessage($message)
    {
        $values = [
            '{name}' => \ucfirst(\request()->user()->name),
            '{at}' => Carbon::now()->format('l, F jS Y, g:i A'),
        ];
        $message = collect(\explode(' ', $message))->map(fn ($word) => isset($values[$word]) ? $values[$word] : $word)->join(' ');
        return $message;
    }
}
