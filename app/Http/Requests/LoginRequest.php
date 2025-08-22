<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login'    => ['required','string'], // email atau NIM
            'password' => ['required','string'],
        ];
    }

    public function messages(): array
    {
        return [
            'login.required'    => 'Email atau NIM wajib diisi.',
            'password.required' => 'Password wajib diisi.',
        ];
    }
}
