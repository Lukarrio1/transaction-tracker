<?php

namespace App\Models;

use App\Models\Reference\Reference;
use Illuminate\Database\Eloquent\Model;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BaseModel extends Model
{
    use HasFactory;
    // use Cachable;

    public function references()
    {
        return $this->hasMany(Reference::class, 'owner_id', 'id');
    }

}
