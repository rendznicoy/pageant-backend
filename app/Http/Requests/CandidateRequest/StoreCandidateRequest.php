<?php

namespace App\Http\Requests\CandidateRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        // Get event_id from route parameter
        $this->merge([
            'event_id' => (int) $this->route('event_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,event_id',
            'candidate_number' => 'required|string',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'sex' => 'required|in:M,F',
            'team' => 'required|string',
            'photo' => 'nullable|image|max:10240|mimes:png,jpg,jpeg',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}