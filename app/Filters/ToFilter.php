<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ToFilter
{
    function __invoke(Builder $query, string $to): Builder
    {
        return $query->where('ends_at', '<=', $to);
    }
}
