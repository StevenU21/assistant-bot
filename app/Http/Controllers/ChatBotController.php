<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Services\OpenAIService;
use App\Http\Requests\ChatBotRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ChatBotController extends Controller
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
        return Inertia::render('ChatBot/Index', compact('user'));
    }

    public function store(ChatBotRequest $request)
    {
        $this->authorize('makeRequest', auth()->user());
        
        $text = $request->validated()['text'];
        $model = $request->validated()['model'];
        $prompt = $request->validated()['prompt'];
        $temperature = $request->validated()['temperature'];

        $response = $this->OpenAIService->conversation($text, $model, $prompt, $temperature);

        return response()->json([
            'bot_message' => $response['bot_message'],
            'input_tokens' => $response['input_tokens'],
            'output_tokens' => $response['output_tokens']
        ]);
    }
}
