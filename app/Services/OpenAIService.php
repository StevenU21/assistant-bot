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

    public function translate($text, $sourceLanguage, $targetLanguage): TranslationResponse
    {
        $response = OpenAI::text()->translate([
            'model' => 'translation-1',
            'input' => $text,
            'source_language' => $sourceLanguage,
            'target_language' => $targetLanguage,
        ]);

        return $response;
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
