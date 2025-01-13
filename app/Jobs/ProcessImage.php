<?php

namespace App\Jobs;

use App\Events\ProcessStatusCompleted;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\Image;

class ProcessImage implements ShouldQueue
{
    use Queueable;

    protected $model;
    protected $prompt;
    protected $style;
    protected $size;
    protected $quality;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($model,$prompt, $style, $size, $quality, $userId)
    {
        $this->model = $model;
        $this->prompt = $prompt;
        $this->style = $style;
        $this->size = $size;
        $this->quality = $quality;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAIService)
    {
        // Generate image using OpenAIService
        $response = $openAIService->textToImage($this->model,$this->prompt, $this->style, $this->size, $this->quality);

        // Extract URL and enhanced prompt from response
        $imageUrl = $response['url'];
        $enhancedPrompt = $response['prompt'];

        // Download image content
        $imageContent = Http::get($imageUrl)->body();

        // Save image file to storage
        $imagePath = 'images/' . uniqid() . '.png';
        Storage::disk('public')->put($imagePath, $imageContent);

        // Create Image record
        Image::create([
            'prompt' => $enhancedPrompt,
            'image_url' => $imagePath,
        ]);

        // Dispatch event
        event(new ProcessStatusCompleted($this->userId, 'Audio generated'));
    }
}
