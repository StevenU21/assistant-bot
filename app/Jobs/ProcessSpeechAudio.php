<?php

namespace App\Jobs;

use App\Events\ProcessStatusCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Models\SpeechAudio;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use App\Models\User;
use Illuminate\Support\Str;

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
        // Generate audio using OpenAIService
        $audioContent = $openAIService->textToSpeech($this->text, $this->voice, $this->model);

        // Get user name and create slug
        $user = User::find($this->userId);
        $userNameSlug = Str::slug($user->name, '-');

        // Create user-specific directory
        $userDirectory = 'speech_audios/' . $userNameSlug;
        if (!Storage::disk('public')->exists($userDirectory)) {
            Storage::disk('public')->makeDirectory($userDirectory);
        }

        // Save audio file to user-specific directory
        $audioPath = $userDirectory . '/' . uniqid() . '.mp3';
        Storage::disk('public')->put($audioPath, $audioContent);

        // Create SpeechAudio record
        SpeechAudio::create([
            'text' => $this->text,
            'voice' => $audioPath,
            'user_id' => $this->userId
        ]);

        // Dispatch event
        event(new ProcessStatusCompleted($this->userId, 'Audio generated'));
    }
}
