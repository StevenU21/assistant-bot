<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
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
            'text' => ['required', 'string', 'min:3', 'max:255'],
            'sourceLanguage' => ['required', 'string', 'in:en,es'],
            'targetLanguage' => ['required', 'string', 'in:en,es'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'text.required' => 'The text field is required.',
            'text.string' => 'The text field must be a string.',
            'text.min' => 'The text field must be at least 3 characters.',
            'text.max' => 'The text field may not be greater than 50 characters.',
            'sourceLanguage.required' => 'The source language field is required.',
            'sourceLanguage.string' => 'The source language field must be a string.',
            'sourceLanguage.in' => 'The source language field must be one of the following: en, es.',
            'targetLanguage.required' => 'The target language field is required.',
            'targetLanguage.string' => 'The target language field must be a string.',
            'targetLanguage.in' => 'The target language field must be one of the following: en, es.',
        ];
    }
}
