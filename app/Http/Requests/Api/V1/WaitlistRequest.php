<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Responses\ApiResponse;

class WaitlistRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'firstName' => ['required', 'string', 'min:2'],
            'lastName' => ['required', 'string', 'min:2'],
            'email' => ['required', 'email'],
            'phoneNumber' => ['required', 'string', 'min:10'],
            'personalityType' => ['nullable', 'string'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'firstName.required' => 'First name is required.',
            'firstName.min' => 'First name must be at least 2 characters.',
            'lastName.required' => 'Last name is required.',
            'lastName.min' => 'Last name must be at least 2 characters.',
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'phoneNumber.required' => 'Phone number is required.',
            'phoneNumber.min' => 'Phone number must be at least 10 digits.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            ApiResponse::error(
                message: 'Validation failed',
                status: 422,
                errors: $validator->errors()->all()
            )
        );
    }
}
