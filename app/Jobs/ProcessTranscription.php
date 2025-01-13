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
        // Obtener la ruta completa del archivo en el sistema
        $absoluteFilePath = Storage::disk('public')->path($this->filePath);

        // Procesar la transcripción
        $response = $openAIService->transcribe($absoluteFilePath, $this->language);

        // Get user name and create slug
        $user = User::find($this->userId);
        $userNameSlug = Str::slug($user->name . '-' . $user->id, '-');

        // Create user-specific directory
        $userDirectory = $userNameSlug . '/transcriptions';
        if (!Storage::disk('public')->exists($userDirectory)) {
            Storage::disk('public')->makeDirectory($userDirectory);
        }

        // Save transcription file to user-specific directory
        $transcriptionPath = $userDirectory . '/' . uniqid() . '.txt';
        Storage::disk('public')->put($transcriptionPath, $response['text']);

        // Guardar en la base de datos
        Transcription::create([
            'title' => $this->fileName,
            'content' => $response['text'],
            'language' => $this->language ?? $response['language'],
            'audio' => $this->filePath, // Guardar la ruta relativa
            'slug' => Str::slug(pathinfo($this->fileName, PATHINFO_FILENAME) . '-' . time()),
            'user_id' => $this->userId
        ]);

        // Despachar evento del fin de la transcripción
        event(new ProcessStatusCompleted($this->userId, 'Transcription completed'));
    }
}
