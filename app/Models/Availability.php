<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['starts_at', 'ends_at', 'slot_duration'])]
class Availability extends Model
{
    /** @use HasFactory<\Database\Factories\AvailabilityFactory> */
    use HasFactory;

    use Filterable;

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
