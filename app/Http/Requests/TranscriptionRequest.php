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
            'file' => ['required', 'file', 'mimes:mp3,m4a', 'max:10240'],
            'language' => ['required', 'string', 'max:255', 'in:en,es,ja,fr,pt'],
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
            'file.required' => 'The file is required.',
            'file.file' => 'The file must be a file.',
            'file.mimes' => 'The file must be a file of type: mp3, m4a.',
            'file.max' => 'The file may not be greater than 10240 kilobytes.',
            'language.required' => 'The language is required.',
            'language.string' => 'The language must be a string.',
            'language.max' => 'The language may not be greater than 255 characters.',
            'language.in' => 'The selected language is invalid.',
        ];
    }
}
