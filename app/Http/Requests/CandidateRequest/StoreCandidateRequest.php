<?php

namespace App\Http\Requests\CandidateRequest;

use Illuminate\Foundation\Http\FormRequest;

class StoreCandidateRequest extends FormRequest
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
            'event_id' => 'required|exists:events,event_id',
            'candidate_number' => 'required|integer',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'sex' => 'required|in:male,female',
            'team' => 'required|string',
            'photo' => 'nullable|image|max:2048',
        ];
    }
}
