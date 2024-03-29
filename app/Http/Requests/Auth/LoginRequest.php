<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginRequest extends FormRequest {
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool {
        return !Auth::check();
//        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required|min:8',
            'remember' => ''
        ];
    }

    public function messages() {
        return [
            'email.required' => 'Укажите почту',
            'email.email' => 'Неправильная почта',
            'email.exists' => 'Почта не зарегистрирована',
            'password.required' => 'Укажите пароль',
            'password.min' => 'Минимальная длина пароля 8 символов'
        ];
    }
}
