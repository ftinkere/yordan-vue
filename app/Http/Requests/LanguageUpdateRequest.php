<?php

namespace App\Http\Requests;

use App\Models\Language;
use Illuminate\Foundation\Http\FormRequest;

class LanguageUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $language = Language::find($this->route('code'));
        return $language && $this->user()->can('edit-language', $language);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|filled',
            'autonym' => '',
            'autonym_transcription' => '',
            'type' => '',
            'status' => '',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Имя не может быть пустым.',
            'name.filled' => 'Имя не может быть пустым.',
        ];
    }
}
