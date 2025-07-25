<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrationRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'nickname' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'birthday' => 'required|date',
            'phone' => 'required|regex:/^\+7\(\d{3}\)\d{3}-\d{2}-\d{2}$/|unique:users,phone',
            'password' => 'required|min:8',
            'confirm_password' => 'required|same:password',
        ];
    }
}
