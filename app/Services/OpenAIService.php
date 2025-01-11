<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Audio\TranslationResponse;

class OpenAIService
{
    public function transcribe($filePath, $language): TranscriptionResponse
    {
        $response = OpenAI::audio()->transcribe([
            'model' => 'whisper-1',
            'file' => fopen($filePath, 'r'),
            'language' => $language,
            'response_format' => 'verbose_json',
        ]);

        return $response;
    }

    public function translate($text, $sourceLanguage, $targetLanguage)
    {
        $prompt = "Translate the following text from $sourceLanguage to $targetLanguage: \"$text\"";

        $response = OpenAI::completions()->create([
            'model' => 'gpt-4o-realtime',
            'prompt' => $prompt,
            'max_tokens' => 70,
            'temperature' => 0.2,
        ]);

        return trim($response['choices'][0]['text']);
    }

    public function textToSpeech($text, $voice, $model = 'tts-1'): string
    {
        $response = OpenAI::audio()->speech([
            'model' => $model, // El modelo ahora se pasa como parámetro
            'input' => $text,
            'voice' => $voice,
        ]);

        return $response;
    }
}
