<?php

namespace App\Http\Requests;

use App\AppointmentStatus;
use App\Order;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetAppointmentsRequest extends FormRequest
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
            'doctor_id' => ['sometimes', 'integer', 'exists:doctors,id'],
            'from'      => ['sometimes', 'date',],
            'to'        => ['sometimes', 'date', 'after_or_equal:from'],
            'status'    => ['sometimes', 'string', Rule::enum(AppointmentStatus::class)],
            'order'     => ['sometimes', 'string', Rule::enum(Order::class)],
        ];
    }
}
