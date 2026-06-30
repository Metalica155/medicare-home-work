<?php

namespace App\Filters;

abstract class Filter
{
    public function __construct(
        protected readonly string $column,
    ) {}
}
