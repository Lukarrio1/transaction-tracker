<?php

use App\Models\ReferenceConfig;
use App\Models\Reference\Reference;

if (!function_exists('createReference')) {
    /**
    * Method createReference
    *
    * @param string $type [Relationship type]
    * @param int $owner_id [owner id]
    * @param int $owned_id [ owned id]
    *
    * @return Reference
    */

    function createReference(string $type, int $owner_id, int $owned_id): Reference|null
    {
        if (empty($type) || empty($owned_id) || empty($owned_id)) {
            return null;
        }
        $config =  ReferenceConfig::where('type', $type)->first();

        if (empty($config)) {
            return null;
        }
        $Reference = Reference::updateOrCreate([
            "owner_id" => $owner_id,
            "owner_model" => $config->owner_model,
            "owned_model" => $config->owned_model,
            "owned_id" => $owned_id,
            "type" => $config->type,
        ], [
            "owner_id" => $owner_id,
            "owner_model" => $config->owner_model,
            "owned_model" => $config->owned_model,
            "owned_id" => $owned_id,
            "type" => $config->type,
        ]);
        return $Reference;
    }


}

if (!function_exists('deleteReference')) {
    /**
    * Method createReference
    *
    * @param string $type [Relationship type]
    * @param int $owner_id [owner id]
   * @param int $owned_id [ owned id]
    *
    * @return bool
    */

    function deleteReference(string $type, int $owner_id, int $owned_id): bool
    {
        if (empty($type) || empty($owned_id)) {
            return false;
        }
        $config =  ReferenceConfig::where('type', $type)->first();

        if (empty($config)) {
            return false;
        }
        $owner_id_query = $owner_id != null ? ["owner_id" => $owner_id] : [];

        Reference::where($owner_id_query + [
            "owner_model" => $config->owner_model,
            "owned_model" => $config->owned_model,
            "owned_id" => $owned_id,
            "type" => $config->type,
        ])->delete();
        return true;
    }
}
