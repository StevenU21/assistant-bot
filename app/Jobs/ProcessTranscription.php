<?php

namespace App\Jobs;

use App\Events\ProcessStatusCompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transcription;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;
use App\Models\User;

class ProcessTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileName;
    protected $language;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $fileName, $language, $userId)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->language = $language;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAIService)
    {
        // Get user name and create slug
        $user = User::find($this->userId);
        $userNameSlug = Str::slug($user->name . '-' . $user->id, '-');

        // Create user-specific directory
        $userDirectory = $userNameSlug . '/transcriptions';
        if (!Storage::disk('public')->exists($userDirectory)) {
            Storage::disk('public')->makeDirectory($userDirectory);
        }

        // Move audio file to user-specific directory
        $userAudioPath = $userDirectory . '/' . basename($this->filePath);
        Storage::disk('public')->move($this->filePath, $userAudioPath);

        // Process the transcription
        $response = $openAIService->transcribe(Storage::disk('public')->path($userAudioPath), $this->language);

        // Format the transcribed text using Markdown
        $formattedText = $openAIService->enrichWithMarkdown($response['text']);

        // Refactor the file name
        $newFileName = $openAIService->refactorName($this->fileName, $formattedText);

        // Save transcription file to user-specific directory
        $transcriptionPath = $userDirectory . '/' . Str::slug($newFileName) . '.txt';
        Storage::disk('public')->put($transcriptionPath, $formattedText);

        // Save to the database
        Transcription::create([
            'title' => $newFileName,
            'content' => $formattedText,
            'language' => $this->language ?? $response['language'],
            'audio' => $userAudioPath, // Save the relative path of the audio
            'slug' => Str::slug($newFileName . '-' . time()),
            'user_id' => $this->userId
        ]);

        // Dispatch event for transcription completion
        event(new ProcessStatusCompleted($this->userId, 'Transcription completed'));
    }
}
