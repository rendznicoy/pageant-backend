<?php

namespace App\Http\Requests\EventRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
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
            'event_name' => 'sometimes|string',
            'event_code' => 'sometimes|string|unique:events,event_code,' . $this->event_id . ',event_id',
            'event_date' => 'sometimes|date',
            'status' => 'sometimes|in:inactive,active,completed',
            'created_by' => 'sometimes|exists:users,user_id',
        ];
    }
}
