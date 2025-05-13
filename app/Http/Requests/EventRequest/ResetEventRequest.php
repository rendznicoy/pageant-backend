<?php

namespace App\Http\Requests\EventRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class ResetEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        $isAuthorized = $user && in_array($user->role, ['admin', 'tabulator']);
        if (!$isAuthorized) {
            Log::warning('Unauthorized attempt to reset event', [
                'user_id' => $user?->user_id,
                'role' => $user?->role,
                'event_id' => $this->route('event_id'),
            ]);
        }
        return $isAuthorized;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'event_id' => $this->route('event_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            'event_id' => 'required|exists:events,event_id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Reset event request failed validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}