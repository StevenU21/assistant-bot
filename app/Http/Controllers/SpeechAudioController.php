<?php

namespace App\Http\Controllers;

use App\Models\SpeechAudio;
use App\Services\OpenAIService;
use Inertia\Inertia;
use Inertia\Response;

class SpeechAudioController extends Controller
{
    protected $OpenAIService;

    public function __construct(OpenAIService $OpenAIService)
    {
        $this->OpenAIService = $OpenAIService;
    }

    public function index(): Response
    {
        $speechAudios = SpeechAudio::latest()->paginate(10);

        $speechAudios->getCollection()->transform(function ($speechAudio) {
            $speechAudio->audioUrl = asset('storage/' . $speechAudio->audio);
            return $speechAudio;
        });

        return Inertia::render('SpeechAudio/Index', compact('speechAudios'));
    }
}
