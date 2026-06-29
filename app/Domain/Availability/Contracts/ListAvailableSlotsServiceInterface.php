<?php

namespace App\Domain\Availability\Contracts;

use App\Domain\Availability\Queries\ListAvailableSlotsQuery;
use Illuminate\Support\Collection;
use App\Domain\Availability\ValueObjects\Slot;

interface ListAvailableSlotsServiceInterface
{
    /**
     * @return Collection<int, Slot>
     */
    public function list(ListAvailableSlotsQuery $query): Collection;
}
