<?php

namespace App\Http\Requests\JudgeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ShowJudgeRequest extends FormRequest
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
            'judge_id' => $this->route('judge_id'),
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
            'judge_id' => 'required|exists:judges,judge_id',
            'event_id' => 'required|exists:events,event_id',
        ];
    }
}
