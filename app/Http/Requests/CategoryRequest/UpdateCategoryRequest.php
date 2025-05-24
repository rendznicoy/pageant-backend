<?php

namespace App\Http\Requests\CategoryRequest;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateCategoryRequest extends FormRequest
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
            'category_weight' => (int) $this->category_weight,
            'max_score' => (int) $this->max_score,
            'category_id' => (int) $this->category_id,
            'event_id' => (int) $this->event_id,
            'stage_id' => (int) $this->stage_id,
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
            'category_id' => 'required|exists:categories,category_id',
            'event_id' => 'required|exists:events,event_id',
            'stage_id' => 'required|exists:stages,stage_id',
            'category_name' => 'required|string|max:255',
            'category_weight' => 'required|integer|min:0|max:100',
            'max_score' => 'required|integer|min:1|max:100',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
