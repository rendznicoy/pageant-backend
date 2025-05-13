<?php

namespace App\Http\Requests\UserRequest;

use Illuminate\Foundation\Http\FormRequest;

class ShowUserRequest extends FormRequest
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
            'username' => 'required|min:3|regex:/^[a-zA-Z0-9_-]+$/|unique:users',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8|regex:/^(?=.*[a-zA-Z])(?=.*[0-9])/|confirmed',
            'first_name' => 'required|min:2|regex:/^[a-zA-Z\s]+$/',
            'last_name' => 'required|min:2|regex:/^[a-zA-Z\s]+$/',
            'role' => 'required|in:admin,tabulator',
        ];
    }
}
