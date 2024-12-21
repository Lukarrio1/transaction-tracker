<?php

namespace App\Models\Reference;

use App\Models\BaseModel;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reference extends BaseModel
{
    use HasFactory;
    protected $fillable = ["owner_id", "owner_model", "owned_model", "owned_id", "type"];

    public function items($types = [])
    {
        $items = collect($types)->map(fn($type) => $this->item($type));
        return $items->toArray();
    }

    public function item($type)
    {
        $this->$type = Cache::get('references')
            ->where('id', $this->id)
            ->where('type', $type)
            ->first();

        if ($this->$type == null) {
            return null;
        }

        $item = $this->$type;
        $id = $item->owned_id;
        $class = new $item->owned_model();
        return $class->find($id)->toArray();
    }
}
