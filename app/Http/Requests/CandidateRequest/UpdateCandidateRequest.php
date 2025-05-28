<?php

namespace App\Http\Requests\CandidateRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UpdateCandidateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = auth()->user();
        $isAuthorized = $user && in_array($user->role, ['admin', 'tabulator']);
        if (!$isAuthorized) {
            Log::warning('Unauthorized update attempt.', [
                'user_id' => $user?->user_id,
                'role' => $user?->role,
            ]);
        }
        return $isAuthorized;
    }

    protected function prepareForValidation(): void
    {
        Log::info('Raw incoming update candidate payload', $this->all());

        $data = [];

        if ($this->has('candidate_id')) {
            $data['candidate_id'] = intval($this->candidate_id);
        }

        if ($this->has('event_id')) {
            $data['event_id'] = intval($this->event_id);
        }

        if ($this->has('is_active')) {
            $data['is_active'] = filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN);
        }

        if ($this->has('team')) {
            $data['team'] = trim($this->team);
        }

        $this->merge($data);
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
            'candidate_number' => 'sometimes|required|string',
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'sex' => 'sometimes|required|in:M,F',
            'team' => 'nullable|string',
            'photo' => 'sometimes|nullable|image|max:10240|mimes:png,jpg,jpeg',
            'is_active' => 'required|boolean',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed for candidate update', [
            'errors' => $validator->errors(),
            'input' => $this->all(),
        ]);

        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
