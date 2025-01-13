<?php

namespace App\Http\Controllers;

use App\Http\Requests\TranslationRequest;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Services\OpenAIService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class TranslationController extends Controller
{
    use AuthorizesRequests;
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function index(): Response
    {
        $user = Auth::user()->load('user_request');
        return Inertia::render('Translations/Index', compact('user'));
    }

    public function translate(TranslationRequest $request): JsonResponse
    {
        $this->authorize('makeRequest', auth()->user());

        $text = $request->validated()['text'];
        $sourceLanguage = $request->validated()['sourceLanguage'];
        $targetLanguage = $request->validated()['targetLanguage'];

        $response = $this->OpenAIService->translate($text, $sourceLanguage, $targetLanguage);

        return response()->json([
            'translatedText' => $response,
        ]);
    }
}
