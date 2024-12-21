<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;

class DynamicModel extends BaseModel
{

    public function setTableName($table)
    {
        $this->setTable($table);
        return $this;
    }

    public function setConnectionName($connection)
    {
        $this->setConnection($connection);
        return $this;
    }

    public function dynamicBelongsTo($database, $relatedTable, $foreignKey, $ownerKey = null)
    {
        $relatedModel = new self();
        // $relatedModel->setConnection($database);
        $relatedModel->setTable($relatedTable);
        return $this->belongsTo(get_class($relatedModel), $foreignKey, $ownerKey);
    }

    public function dynamicHasMany($database, $relatedTable, $foreignKey, $localKey = null)
    {

        $relatedModel = new self();
        $relatedModel->setConnection($database);
        $relatedModel->setTable($relatedTable);
        return $this->hasMany(get_class($relatedModel), $foreignKey, $localKey);

    }


}
