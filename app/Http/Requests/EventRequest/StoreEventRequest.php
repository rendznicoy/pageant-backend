<?php
namespace App\Http\Requests\EventRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Log;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = auth()->user();
        $isAuthorized = $user && in_array($user->role, ['admin', 'tabulator']);
        if (!$isAuthorized) {
            Log::warning('Unauthorized attempt to create event', [
                'user_id' => $user?->user_id,
                'role' => $user?->role,
            ]);
        }
        return $isAuthorized;
    }

    protected function prepareForValidation()
    {
        if ($this->has('statisticians') && is_string($this->statisticians)) {
            $decoded = json_decode($this->statisticians, true);
            $this->merge(['statisticians' => $decoded]);
        }
    }

    public function rules(): array
    {
        return [
            'event_name' => 'required|string|max:255',
            'venue' => 'required|string|max:255',
            'start_date' => 'required|date_format:Y-m-d H:i:s',
            'end_date' => 'required|date_format:Y-m-d H:i:s|after_or_equal:start_date',
            'description' => 'nullable|string',
            'cover_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'division' => 'required|in:standard,male-only,female-only',
            'global_max_score' => 'sometimes|integer|min:1|max:100',
            'statisticians' => 'required|array|min:1',
            'statisticians.*.id' => 'nullable|integer',
            'statisticians.*.name' => 'required|string',
            'created_by' => 'required|exists:users,user_id',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        Log::error('Create event validation failed', [
            'errors' => $validator->errors()->all(),
            'input' => $this->all(),
        ]);
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Create event request failed validation.',
            'errors' => $validator->errors(),
        ], 422));
    }
}