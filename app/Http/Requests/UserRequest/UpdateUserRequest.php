<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'username' => 'sometimes|string|min:3|unique:users,username,' . $this->user->user_id . ',user_id',
            'email' => 'sometimes|email|unique:users,email,' . $this->user->user_id . ',user_id',
            'password' => 'nullable|string|min:8',
            'first_name' => 'sometimes|string',
            'last_name' => 'sometimes|string',
            'role' => 'sometimes|in:Admin,Tabulator,Judge',
        ];
    }
}
