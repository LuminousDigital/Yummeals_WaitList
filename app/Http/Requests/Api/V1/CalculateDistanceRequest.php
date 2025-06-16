<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class CalculateDistanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'origin' => 'nullable|string|regex:/^[-]?[0-9]{1,2}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/', // Optional lat,lng
            'destinations' => 'required|array|min:1',
            'destinations.*' => 'required|string|regex:/^[-]?[0-9]{1,2}\.[0-9]+,[-]?[0-9]{1,3}\.[0-9]+$/', // Array of lat,lng
        ];
    }

    public function messages(): array
    {
        return [
            'origin.regex' => 'Origin must be in latitude,longitude format (e.g., 40.6655101,-73.8918896).',
            'destinations.required' => 'At least one destination is required.',
            'destinations.array' => 'Destinations must be an array of coordinates.',
            'destinations.*.regex' => 'Each destination must be in latitude,longitude format (e.g., 40.659569,-73.933783).',
        ];
    }
}
