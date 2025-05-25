<?php

namespace App\Http\Requests\EventRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class UpdateEventRequest extends FormRequest
{
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
        $raw = $this->request; // Access Symfony's underlying input bag

        $eventName = $raw->get('event_name');
        $venue = $raw->get('venue');
        $description = $raw->get('description');
        $start = $raw->get('start_date');
        $end = $raw->get('end_date');

        try {
            $this->merge([
                'event_name' => $eventName,
                'venue' => $venue,
                'description' => $description,
                'start_date' => $start ? \Carbon\Carbon::parse($start)->format('Y-m-d H:i:s') : null,
                'end_date' => $end ? \Carbon\Carbon::parse($end)->format('Y-m-d H:i:s') : null,
            ]);
        } catch (\Exception $e) {
            Log::error('prepareForValidation error:', [
                'error' => $e->getMessage(),
                'start_date_raw' => $start,
                'end_date_raw' => $end,
            ]);
        }

        Log::info('After prepareForValidation (using $this->request->get()):', $this->all());
    }

    public function rules(): array
    {
        return [
            'event_name' => 'sometimes|required|string|max:255',
            'venue' => 'sometimes|required|string|max:255',
            'start_date' => 'sometimes|required|date_format:Y-m-d H:i:s',
            'end_date' => 'sometimes|required|date_format:Y-m-d H:i:s|after_or_equal:start_date',
            'description' => 'nullable|string',
            'division' => 'sometimes|required|in:standard,male-only,female-only',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'global_max_score' => 'sometimes|integer|min:1|max:100',
            'statisticians' => 'sometimes|array|min:1',
            'statisticians.*.id' => 'nullable|integer',
            'statisticians.*.name' => 'sometimes|string',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Validation failed.', [
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
