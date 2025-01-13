<?php

namespace App\Http\Controllers;

use App\Events\ProcessStatusStarted;
use App\Http\Requests\TextToSpeechRequest;
use App\Jobs\ProcessSpeechAudio;
use App\Models\SpeechAudio;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Auth;

class SpeechAudioController extends Controller
{
    use AuthorizesRequests;

    public function index(): Response
    {
        $user = Auth::user()->load('user_request');
        $speechAudios = SpeechAudio::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $speechAudios->getCollection()->transform(function ($speechAudio) {
            $speechAudio->audioUrl = asset('storage/' . $speechAudio->voice);
            return $speechAudio;
        });

        return Inertia::render('SpeechAudio/Index', compact('speechAudios'));
    }

    public function store(TextToSpeechRequest $request)
    {
        $this->authorize('makeRequest', auth()->user());
        // Get request data
        $text = $request->validated()['text'];
        $voice = $request->validated()['voice'];
        $model = $request->validated()['model'];

        event(new ProcessStatusStarted(auth()->id(), 'Generating audio'));

        // Depach job
        ProcessSpeechAudio::dispatch($text, $voice, $model, auth()->id());

        // Redirect to index
        return redirect()->route('speech_audios.index');
    }

    public function download_audio($id)
    {
        $speechAudio = SpeechAudio::findOrFail($id);

        return Storage::disk('public')->download($speechAudio->voice);
    }
    public function download_text($id)
    {
        $speechAudio = SpeechAudio::findOrFail($id);

        $data = [
            'audio' => $speechAudio->audio,
            'text' => $speechAudio->text,
            'voice' => $speechAudio->voice,
        ];

        $pdf = PDF::loadView('speechAudio.pdf', $data);

        return $pdf->download($speechAudio->id . '-' . 'speech-audio.pdf');
    }

    public function destroy($id)
    {
        $speechAudio = SpeechAudio::findOrFail($id);
        Storage::disk('public')->delete($speechAudio->voice);
        $speechAudio->delete();

        return redirect()->route('speech_audios.index');
    }

}
