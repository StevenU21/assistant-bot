<?php

namespace App\Jobs;

use App\Events\ProcessStatusCompleted;
use App\Services\OpenAIService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Image;
use Illuminate\Support\Str;

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
        $response = $openAIService->textToImage($this->model, $this->prompt, $this->style, $this->size, $this->quality);

        // Extract URL and enhanced prompt from response
        $imageUrl = $response['url'];
        $enhancedPrompt = $response['prompt'];

        // Download image content
        $imageContent = Http::get($imageUrl)->body();

        // Get user name
        $user = User::find($this->userId);
        $userName = Str::slug($user->name, '-');

        // Create user-specific directory
        $userDirectory = 'images/' . $userName;
        if (!Storage::disk('public')->exists($userDirectory)) {
            Storage::disk('public')->makeDirectory($userDirectory);
        }

        // Save image file to user-specific directory
        $imagePath = $userDirectory . '/' . uniqid() . '.png';
        Storage::disk('public')->put($imagePath, $imageContent);

        // Create Image record
        Image::create([
            'prompt' => $enhancedPrompt,
            'image_url' => $imagePath,
            'user_id' => $this->userId
        ]);

        // Dispatch event
        event(new ProcessStatusCompleted($this->userId, 'Image generated'));
    }
}
