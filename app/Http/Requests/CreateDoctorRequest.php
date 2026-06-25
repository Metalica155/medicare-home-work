<?php

namespace App\Http\Requests;

use App\Expertise;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateDoctorRequest extends FormRequest
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
            'name'      => ['required', 'max:255'],
            'email'     => ['required', 'email:rfc', 'max:255', 'unique:doctors,email'],
            'expertise' => ['required', Rule::enum(Expertise::class)]
        ];
    }
}
