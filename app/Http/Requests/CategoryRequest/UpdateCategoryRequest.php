<?php

namespace App\Http\Requests\CategoryRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
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
            'category_id' => 'required|exists:categories,category_id',
            'event_id' => 'required|exists:events,event_id',
            'category_name' => 'sometimes|string',
            'category_weight' => 'sometimes|numeric|min:0|max:100',
            'max_score' => 'sometimes|integer|min:1|max:10',
        ];
    }
}
