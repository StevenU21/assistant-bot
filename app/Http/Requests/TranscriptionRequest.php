<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranscriptionRequest extends FormRequest
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
            'audio' => ['required', 'file', 'mimes:mp3,m4a', 'max:10240'], // 10MB max
            'language' => ['required', 'string', 'in:auto,en,es,fr,pt'],
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
            'audio.required' => 'The audio file is required.',
            'audio.file' => 'The audio file must be a file.',
            'audio.mimes' => 'The audio file must be a file of type: mp3, m4a.',
            'audio.max' => 'The audio file may not be greater than 10MB.',
            'language.required' => 'The language is required.',
            'language.string' => 'The language must be a string.',
            'language.in' => 'The selected language is invalid or not supported.',
        ];
    }
}
