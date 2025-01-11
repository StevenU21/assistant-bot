<?php

namespace App\Http\Controllers;

use App\Events\SpeechAudioStarted;
use App\Http\Requests\TextToSpeechRequest;
use App\Jobs\ProcessSpeechAudio;
use App\Models\SpeechAudio;
use App\Services\OpenAIService;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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
            $speechAudio->audioUrl = asset('storage/' . $speechAudio->voice);
            return $speechAudio;
        });

        return Inertia::render('SpeechAudio/Index', compact('speechAudios'));
    }

    public function store(TextToSpeechRequest $request)
    {
        // Get request data
        $text = $request->validated()['text'];
        $voice = $request->validated()['voice'];
        $model = $request->validated()['model'];

        // Depach job
        ProcessSpeechAudio::dispatch($text, $voice, $model, auth()->id());

        // Redirect to index
        return redirect()->route('speech_audios.index');
    }

    public function download($id)
    {
        $speechAudio = SpeechAudio::findOrFail($id);
        
        return Storage::disk('public')->download($speechAudio->voice);
    }

    public function destroy($id)
    {
        $speechAudio = SpeechAudio::findOrFail($id);
        Storage::disk('public')->delete($speechAudio->voice);
        $speechAudio->delete();

        return redirect()->route('speech_audios.index');
    }

}
