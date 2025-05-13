<?php
namespace App\Http\Requests\EventRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        $isAuthorized = $user && in_array($user->role, ['admin', 'tabulator']);
        if (!$isAuthorized) {
            Log::warning('Unauthorized attempt to update event', [
                'user_id' => $user?->user_id,
                'role' => $user?->role,
                'event_id' => $this->route('event_id'),
            ]);
        }
        return $isAuthorized;
    }

    protected function prepareForValidation(): void
    {
        Log::info('Preparing validation for update event', [
            'input' => $this->all(),
            'event_id' => $this->event_id,
        ]);

        // Ensure event_id is included
        if ($this->has('event_id')) {
            $this->merge(['event_id' => $this->event_id]);
        }
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'exists:events,event_id'],
            'event_name' => ['sometimes', 'string', 'max:255'],
            'event_code' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'description' => ['sometimes', 'string', 'nullable'],
            'cover_photo' => ['sometimes', 'image', 'mimes:jpeg,png,jpg,gif', 'max:5120'], // 5MB max
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Update event validation failed', [
            'errors' => $validator->errors()->all(),
            'input' => $this->all(),
        ]);
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Update event request failed validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}