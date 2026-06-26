<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
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
            'name'         => ['sometimes', 'string', 'max:255'],
            'email'        => [
                'sometimes',
                'email:rfc',
                'max:255',
                Rule::unique('patients')->ignore($this->route('patient')),
            ],
            'phone_number' => ['sometimes', 'regex:/^\+?[0-9\s\-\(\)]{7,20}$/'],
        ];
    }
}
