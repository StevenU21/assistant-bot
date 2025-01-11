<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImageRequest extends FormRequest
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
            'prompt' => ['required', 'string', 'min:12', 'max:150'],
            'style' => ['required', 'string', 'in:realistic,anime,cartoon,futuristic,abstract'],
            'size' => ['required', 'string', 'in:tiny,small,medium,large,huge'],
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
            'prompt.required' => 'The prompt field is required.',
            'prompt.string' => 'The prompt field must be a string.',
            'prompt.min' => 'The prompt field must be at least :min characters.',
            'prompt.max' => 'The prompt field may not be greater than :max characters.',
            'style.required' => 'The style field is required.',
            'style.string' => 'The style field must be a string.',
            'style.in' => 'The selected style is invalid.',
            'size.required' => 'The size field is required.',
            'size.string' => 'The size field must be a string.',
            'size.in' => 'The selected size is invalid.',
        ];
    }
}
