<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class ListAvailableSlotsRequest extends FormRequest
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
            'from'      => ['sometimes', 'date', 'required_with:to'],
            'to'        => ['sometimes', 'date', 'required_with:from', 'after:from'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if ($this->isNotFilled('doctor_id') && !($this->filled('from') && $this->filled('to'))) {
                    $validator->errors()->add(
                        'doctor_id',
                        'Either doctor_id or both from and to must be provided.'
                    );
                }
            },
        ];
    }
}
