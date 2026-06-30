<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'start_time'    => Carbon::parse($this->start_time)->toRfc3339String(),
            'end_time'      => Carbon::parse($this->end_time)->toRfc3339String(),
            'status'        => $this->status,
            'cancel_reason' => $this->cancel_reason,
            'doctor'        => new DoctorResource($this->doctor),
            'patient'       => new PatientResource($this->patient),
        ];
    }
}
