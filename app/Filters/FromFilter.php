<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class FromFilter
{
    function __invoke(Builder $query, string $from): Builder
    {
        return $query->where('starts_at', '>=', $from);
    }
}
