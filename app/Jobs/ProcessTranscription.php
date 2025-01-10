<?php

namespace App\Jobs;

use App\Events\TranscriptionStarted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transcription;
use Illuminate\Support\Str;
use App\Events\TranscriptionCompleted;
use Illuminate\Support\Facades\Storage;
use App\Services\OpenAIService;

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
        event(new TranscriptionStarted($this->userId));
        // Obtener la ruta completa del archivo en el sistema
        $absoluteFilePath = Storage::disk('public')->path($this->filePath);

        // Procesar la transcripción
        $response = $openAIService->transcribe($absoluteFilePath, $this->language);

        // Guardar en la base de datos
        Transcription::create([
            'title' => $this->fileName,
            'content' => $response['text'],
            'language' => $this->language ?? $response['language'],
            'audio' => $this->filePath, // Guardar la ruta relativa
            'slug' => Str::slug(pathinfo($this->fileName, PATHINFO_FILENAME) . '-' . time()),
        ]);

        // Despachar evento del fin de la transcripción
        event(new TranscriptionCompleted($this->userId));
    }
}
