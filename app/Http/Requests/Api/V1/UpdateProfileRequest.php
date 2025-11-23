<?php

namespace App\Http\Requests\Api\V1;

use App\Enums\Members\IdType;
use App\Enums\Members\PreferredCommunication;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['sometimes', 'filled', 'string', 'max:50'],
            'last_name' => ['sometimes', 'filled', 'string', 'max:50'],
            'qatar_id_or_passport' => ['nullable', 'string', 'max:50'],
            'id_type' => ['nullable', Rule::enum(IdType::class)],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'phone' => ['nullable', 'string', 'max:20'],
            'preferred_communication' => ['nullable', Rule::enum(PreferredCommunication::class)],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'first_name.required' => 'First name is required.',
            'last_name.required' => 'Last name is required.',
            'date_of_birth.before' => 'Date of birth must be before today.',
            'id_type.enum' => 'ID type must be either QATAR_ID or PASSPORT.',
            'preferred_communication.enum' => 'Preferred communication must be either EMAIL or SMS.',
        ];
    }
}
