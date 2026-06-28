<?php

namespace App\Filters;

use Illuminate\Database\Eloquent\Builder;

abstract class Filters
{
    protected $filters = [];

    public function apply(Builder $query)
    {
        foreach ($this->receivedFilters() as $name => $value) {
            $filterInstance = new $this->filters[$name];
            $query = $filterInstance($query, $value);
        }

        return $query;
    }

    private function receivedFilters()
    {
        return request()->only(array_keys($this->filters));
    }
}
