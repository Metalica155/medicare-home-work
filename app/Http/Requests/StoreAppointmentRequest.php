<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id'  => ['required', 'integer', 'exists:doctors,id'],
            'start_time' => ['required', 'date', 'after:now'],
            'end_time'   => ['required', 'date', 'after:start_time'],
        ];
    }
}
