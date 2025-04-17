<?php

namespace App\Http\Requests\EventRequest;

use Illuminate\Foundation\Http\FormRequest;

class ShowEventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    
    protected function prepareForValidation()
    {
        $this->merge([
            'event_id' => $this->route('event_id'),
        ]);
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
        ];
    }
}
