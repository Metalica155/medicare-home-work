<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

class StatusFilter extends Filter
{
    function __invoke(Builder $query, string $status): Builder
    {
        return $query->where($this->column, '=', $status);
    }
}
