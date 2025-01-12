<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Audio\TranscriptionResponse;
use OpenAI\Responses\Models\ListResponse;
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

    public function textToImage($model = 'dall-e-2', $prompt, $style, $size, $quality = 'standard')
    {
        $styleDescriptions = [
            'realistic' => 'with photorealistic details and natural lighting',
            'anime' => 'with vibrant colors, anime-style shading, and expressive characters',
            'cartoon' => 'with bold lines, simple shapes, and bright colors',
            'futuristic' => 'featuring advanced technology and a sci-fi atmosphere',
            'abstract' => 'with surreal and abstract forms, blending colors and shapes uniquely',
        ];

        $styleDescription = $styleDescriptions[$style] ?? 'in an artistic style';

        $enhancedPrompt = "{$prompt} {$styleDescription}.";

        $response = OpenAI::images()->create([
            'model' => $model,
            'prompt' => $enhancedPrompt,
            'size' => $size,
            'quality' => $quality,
            'response_format' => 'url',
        ]);

        return [
            'url' => $response['data'][0]['url'],
            'prompt' => $enhancedPrompt,
        ];
    }

    public function conversation($text, $model, $prompt = 'assistant', $temperature)
    {
        $promptDescription = [
            'assistant' => 'You are an AI assistant. Provide helpful responses to user queries.',
            'grammar_correction' => 'You will be provided with statements, and your task is to convert them to standard English.',
            'sarcastic_response' => 'You are Marv, a chatbot that reluctantly answers questions with sarcastic responses.',
            'code_explainer' => 'You will be provided with a piece of code, and your task is to explain it in a concise way.',
            'simplify_text' => 'You will be provided with a complex text, and your task is to simplify it.',
            'code_interviewer' => 'You are an interviewer, and you will be asking questions to a candidate about their code.',
            'improve_code_efficiency' => 'You will be provided with a piece of code, and your task is to provide ideas for efficiency improvements.',
            'translator' => 'You are a translator, and you will be translating text from one language to another.',
            'psychologist' => 'Act as a professional psychologist. Your goal is to provide empathetic, understanding, and helpful responses, guiding the user in exploring their thoughts and emotions reflectively. Do not replace professional advice, but offer emotional support and tools to help them cope with their issues. Ask open-ended questions to help the person dive deeper into their thoughts and emotions. Use a kind, attentive, and non-judgmental tone.',
        ];

        $messages = [
            ['role' => 'system', 'content' => $promptDescription[$prompt] ?? $promptDescription['assistant']],
            ['role' => 'user', 'content' => $text]
        ];

        $response = OpenAI::chat()->create([
            'model' => $model,
            'messages' => $messages,
            'temperature' => $temperature,
        ]);

        return [
            'bot_message' => $response['choices'][0]['message']['content'],
            'input_tokens' => $response['usage']['prompt_tokens'],
            'output_tokens' => $response['usage']['completion_tokens']
        ];
    }

    public function getAIModels(): ListResponse
    {
        $response = OpenAI::models()->list();

        return $response;
    }
}
