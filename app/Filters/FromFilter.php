<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class FromFilter extends Filter
{
    function __invoke(Builder $query, string $from): Builder
    {
        return $query->where($this->column, '>=', $from);
    }
}
