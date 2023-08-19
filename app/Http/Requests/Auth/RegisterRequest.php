<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:2',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'password_confirmation' => 'required|same:password',
            'check' => 'required|accepted',
        ];
    }

    public function messages() {
        return [
            'name.required' => 'Укажите имя',
            'name.min' => 'Укажите имя как минимум в два символа',
            'email.required' => 'Укажите почту',
            'email.unique' => 'Почта уже используется',
            'password.required' => 'Укажите пароль',
            'password.min' => 'Длина пароля минимум 8 символов',
            'password_confirmation.required' => 'Подтвердите пароль',
            'password_confirmation.same' => 'Пароли не совпадают',
            'check.required' => 'Вы не согласны с условиями',
            'check.accepted' => 'Вы не согласны с условиями',
        ];
    }
}
