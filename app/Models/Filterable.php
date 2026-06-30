<?php

namespace App\Models;

use App\Filters\Filters;
use Illuminate\Database\Eloquent\Builder;

trait Filterable
{
    public function scopeFilter(Builder $query, Filters $filters)
    {
        return $filters->apply($query);
    }
}
