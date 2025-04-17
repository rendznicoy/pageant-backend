<?php

namespace App\Http\Requests\ScoreRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\Rule;

class DestroyScoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
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
            'judge_id' => [
                'required',
                Rule::exists('judges', 'judge_id')->where('event_id', $this->input('event_id')),
            ],
            'candidate_id' => [
                'required',
                Rule::exists('candidates', 'candidate_id')->where('event_id', $this->input('event_id')),
            ],
            'category_id' => [
                'required',
                Rule::exists('categories', 'category_id')->where('event_id', $this->input('event_id')),
            ],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Failed to validate score deletion.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
