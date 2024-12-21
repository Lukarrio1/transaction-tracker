<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ReferenceConfig extends BaseModel
{
    use HasFactory;
    protected $fillable = ['id', 'owned_model', 'owner_model', 'type', 'description'];
}
