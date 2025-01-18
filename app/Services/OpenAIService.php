<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
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

    public function textToImage($prompt, $style, $size, $model = 'dall-e-2', $quality = 'standard')
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

    public function conversation($text, $model, $temperature, $prompt = 'assistant')
    {
        $messages = [
            ['role' => 'system', 'content' => $this->getPromptDescription($prompt)],
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

    public function enrichWithMarkdown($text, $context = '')
    {
        $prompt = "Take the following text and format it using Markdown:\n\n" .
            "Text:\n$text\n\n" .
            ($context ? "Additional context: $context\n\n" : "") .
            "Instructions:\n- Use headings for relevant titles.\n- Use lists if there are enumerated items.\n- Use bold to highlight important words.\n- Use code blocks if there are technical snippets.\n- If it's a song lyric, format verses, paragraphs, separate the text into introduction, verses, chorus, bridge, and ending.\n- Adapt the formatting depending on whether it's a story, narration, dictation, etc.\n- At the end of the text, add an extra section for analysis, reflections, and context.\n\n" .
            "After formatting the text in Markdown, provide a detailed analysis of the text. The analysis should include:\n- Summary of the main points.\n- Interpretation of the text's meaning.\n- Any notable stylistic elements.\n- Contextual information that enhances understanding.\n\nReturn only the text in Markdown followed by the analysis.";

        $response = OpenAI::chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that formats text in Markdown and provides detailed analysis.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }

    public function refactorName($filename, $context = '')
    {
        $response = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an assistant that reads the audio file name and, depending on the context, simplifies it to natural text. If the audio file has no name, use the context to generate a name, e.g., Greeting, Horror Story, etc., with a maximum of 40 characters. Avoid generic texts like "audio file". Format the name as "Name - Author (if mentioned) - Something about the song".'],
                ['role' => 'user', 'content' => $filename . ' - ' . $context],
            ],
        ]);

        return $response['choices'][0]['message']['content'];
    }

    private function getPromptDescription($prompt)
    {
        $globalInstruction = 'Use Markdown format whenever appropriate to enrich your responses. Add separation lines, titles, spacing, and any elements that help make the content look clean and professional. Avoid separating paragraphs too much. If presenting code, use Markdown blocks to highlight it from the rest of the text.';

        $promptDescription = [
            'assistant' => 'You are an AI assistant. Provide helpful responses to user queries.',
            'grammar_correction' => 'You will be provided with statements, and your task is to convert them to standard English.',
            'sarcastic_response' => 'You are Marv, a chatbot that reluctantly answers questions with sarcastic answers and use emojis, and you also express yourself tiresomely as if the user is coming to get in the way and steal your peace. Every chance you get, try to tease the user by giving funny facts, and also use word games. By that I mean expressing yourself in a very informal and sometimes confusing way in order to keep the user entertained and want to continue asking you or saying things, you are a great guy who knows a little about everything and it tastes like life.',
            'code_explainer' => 'You will be provided with a piece of code, and your task is to explain it in a concise way.',
            'simplify_text' => 'You will be provided with a complex text, and your task is to simplify it.',
            'code_interviewer' => 'You are an interviewer, and you will be asking questions to a candidate about their code.',
            'improve_code_efficiency' => 'You will be provided with a piece of code, and your task is to provide ideas for efficiency improvements.',
            'translator' => 'You are a translator, and you will be translating text from one language to another.',
            'psychologist' => 'Act as a professional psychologist. Your goal is to provide empathetic, understanding, and helpful responses, guiding the user in exploring their thoughts and emotions reflectively. Do not replace professional advice, but offer emotional support and tools to help them cope with their issues. Ask open-ended questions to help the person dive deeper into their thoughts and emotions. Use a kind, attentive, and non-judgmental tone.',
        ];

        return ($promptDescription[$prompt] ?? $promptDescription['assistant']) . $globalInstruction;
    }

    public function getAIModels(): ListResponse
    {
        $response = OpenAI::models()->list();

        return $response;
    }

    public function getPromptList(): array
    {
        $response = Http::get('https://raw.githubusercontent.com/f/awesome-chatgpt-prompts/main/prompts.csv');

        if ($response->successful()) {
            $csvData = $response->body();
            $lines = explode(PHP_EOL, $csvData);
            $prompts = array_map('str_getcsv', $lines);
            return $prompts;
        }

        return [];
    }
}
