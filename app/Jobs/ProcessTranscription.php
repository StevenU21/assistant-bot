<?php

namespace App\Jobs;

use App\Models\Task;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Transcription;
use App\Services\OpenAIService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


class ProcessTranscription implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $filePath;
    protected $fileName;
    protected $language;

    /**
     * Create a new job instance.
     */
    public function __construct($filePath, $fileName, $language)
    {
        $this->filePath = $filePath;
        $this->fileName = $fileName;
        $this->language = $language;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAIService)
    {
        // Obtener la ruta completa del archivo en el sistema
        $absoluteFilePath = Storage::disk('public')->path($this->filePath);

        // Procesar la transcripción
        // $response = $openAIService->transcribe($absoluteFilePath, $this->language);

        $contentMessage = "Transcripción de audio completada";
        $languajeMessage = "es";

        sleep(3);
        // Guardar en la base de datos
        Transcription::create([
            'title' => $this->fileName,
            'content' => $contentMessage,
            'language' => $languajeMessage,
            // 'content' => $response['text'],
            // 'language' => $this->language ?? $response['language'],
            'audio' => $this->filePath, // Guardar la ruta relativa
            'slug' => Str::slug(pathinfo($this->fileName, PATHINFO_FILENAME) . '-' . time()),
        ]);
    }
}
