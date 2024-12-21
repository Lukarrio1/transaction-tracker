<?php

namespace App;

trait HasCustomPagination
{
    public function scopeCustomPaginate($q, $perPage, $page)
    {

        $q->skip((int) $perPage * (int) $page - (int) $perPage)
            ->take((int) $perPage);
    }
}
