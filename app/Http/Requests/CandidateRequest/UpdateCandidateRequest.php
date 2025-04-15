<?php

namespace App\Http\Requests\CandidateRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCandidateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'event_id' => 'required|exists:events,event_id',
            'candidate_number' => 'sometimes|string',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'sex' => 'sometimes|in:male,female',
            'team' => 'sometimes|string',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
