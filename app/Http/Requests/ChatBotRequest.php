<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChatBotRequest extends FormRequest
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
            'text' => ['required', 'string', 'min:4', 'max:600'],
            'model' => ['required', 'string', 'in:gpt-3.5-turbo,gpt-4o,gpt-4o-mini'],
            'prompt' => ['required', 'string', 'in:assistant,grammar_correction,sarcastic_response,code_explainer,simplify_text,code_interviewer,improve_code_efficiency,translator,psychologist'],
            'temperature' => ['required', 'numeric', 'min:0', 'max:1.5'],
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
            'model.required' => 'The model field is required.',
            'model.string' => 'The model field must be a string.',
            'model.in' => 'The selected model is invalid.',
            'prompt.required' => 'The prompt field is required.',
            'prompt.string' => 'The prompt field must be a string.',
            'temperature.required' => 'The temperature field is required.',
            'temperature.numeric' => 'The temperature field must be a number.',
            'temperature.min' => 'The temperature field must be at least :min.',
            'temperature.max' => 'The temperature field may not be greater than :max.',
        ];
    }
}
