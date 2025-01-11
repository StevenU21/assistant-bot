<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TextToSpeechRequest extends FormRequest
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
            'text' => ['required', 'string', 'min:2', 'max:3000'],
            'voice' => ['required', 'string', 'in:alloy,ash,coral,echo,fable,onyx,nova,sage,shimmer'],
            'model' => ['required', 'string', 'in:tts-1,tts-1-hd'],
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
            'text.min' => 'The text field must be at least :min characters.',
            'text.max' => 'The text field may not be greater than :max characters.',
            'voice.required' => 'The voice field is required.',
            'voice.string' => 'The voice field must be a string.',
            'voice.in' => 'The selected voice is invalid.',
            'model.required' => 'The model field is required.',
            'model.string' => 'The model field must be a string.',
            'model.in' => 'The selected model is invalid.',
        ];
    }
}
