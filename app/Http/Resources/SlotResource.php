<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SlotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'availability_id' => $this->availabilityId,
            'doctor_id'       => $this->doctorId,
            'starts_at'       => $this->startsAt->toRfc3339String(),
            'ends_at'         => $this->endsAt->toRfc3339String(),
            'duration'        => $this->duration,
        ];
    }
}
