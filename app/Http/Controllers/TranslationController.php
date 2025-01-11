<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\OpenAIService;

class TranslationController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function index(): Response
    {
        return Inertia::render('Translations/Index');
    }

    public function translate(TranslationRequest $request): JsonResponse
    {
        $text = $request->validated()['text'];
        $sourceLanguage = $request->validated()['sourceLanguage'];
        $targetLanguage = $request->validated()['targetLanguage'];

        $response = $this->OpenAIService->translate($text, $sourceLanguage, $targetLanguage);

        return response()->json([
            'translatedText' => $response,
        ]);
    }
}
