<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\SpeechAudio;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use App\Events\SpeechAudioStarted;
use App\Events\SpeechAudioCompleted;

class ProcessSpeechAudio implements ShouldQueue
{
    use Queueable;

    protected $text;
    protected $voice;
    protected $model;

    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($text, $voice, $model, $userId)
    {
        $this->text = $text;
        $this->voice = $voice;
        $this->model = $model;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */

    public function handle(OpenAIService $openAIService)
    {
        // Dispatch event
        event(new SpeechAudioStarted($this->userId));
        // Generate audio using OpenAIService
        $audioContent = $openAIService->textToSpeech($this->text, $this->voice, $this->model);

        // Save audio file to storage
        $audioPath = 'speech_audios/' . uniqid() . '.mp3';
        Storage::disk('public')->put($audioPath, $audioContent);

        // Create SpeechAudio record
        SpeechAudio::create([
            'text' => $this->text,
            'voice' => $audioPath,
        ]);

        // Dispatch event
        event(new SpeechAudioCompleted($this->userId));
    }
}
