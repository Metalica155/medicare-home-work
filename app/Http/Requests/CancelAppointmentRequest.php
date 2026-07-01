<?php

namespace App\Http\Requests;

use App\Models\Appointment;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CancelAppointmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Appointment $appointment */
        $appointment = $this->route('appointment');

        return $appointment->doctor_id == $this->doctor_id
            && $appointment->patient_id == $this->patient_id;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id'   => ['required', 'integer', 'exists:doctors,id'],
            'patient_id'  => ['required', 'integer', 'exists:patients,id'],
            'reason'      => ['required', 'string', 'max:255'],
        ];
    }
}
