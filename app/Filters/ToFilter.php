<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class ToFilter extends Filter
{
    function __invoke(Builder $query, string $to): Builder
    {
        return $query->where($this->column, '<=', $to);
    }
}
