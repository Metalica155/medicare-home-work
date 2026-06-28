<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AvailabilityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'starts_at'     => $this->starts_at,
            'ends_at'       => $this->ends_at,
            'slot_duration' => $this->slot_duration,
            'doctor'        => new DoctorResource($this->doctor),
        ];
    }
}
