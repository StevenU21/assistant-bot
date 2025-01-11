<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
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
        $messages = [
            ['role' => 'system', 'content' => "You are a translator."],
            ['role' => 'user', 'content' => "Translate the following text from $sourceLanguage to $targetLanguage: $text"]
        ];

        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => $messages,
            'max_tokens' => 70,
            'temperature' => 0,
        ]);

        return trim($response['choices'][0]['message']['content']);
    }

    public function textToSpeech($text, $voice, $model = 'tts-1', $responseFormat = 'mp3')
    {
        $response = OpenAI::audio()->speech([
            'model' => $model,
            'input' => $text,
            'voice' => $voice,
            'response_format' => $responseFormat,
        ]);

        return $response;
    }
}
