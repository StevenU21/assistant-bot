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
    protected $prompt;
    protected $style;
    protected $size;
    protected $model;
    protected $quality;
    protected $userId;

    /**
     * Create a new job instance.
     */
    public function __construct($prompt, $style, $size, $model, $quality, $userId)
    {
        $this->prompt = $prompt;
        $this->style = $style;
        $this->size = $size;
        $this->model = $model;
        $this->quality = $quality;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(OpenAIService $openAIService)
    {
        // Generate image using OpenAIService
        $response = $openAIService->textToImage($this->prompt, $this->style, $this->size,$this->model, $this->quality);

        // Extract URL and enhanced prompt from response
        $imageUrl = $response['url'];
        $enhancedPrompt = $response['prompt'];

        // Download image content
        $imageContent = Http::get($imageUrl)->body();

        // Get user name
        $user = User::find($this->userId);
        $userNameSlug = Str::slug($user->name . '-' . $user->id, '-');

        // Create user-specific directory
        $userDirectory = $userNameSlug . '/images';
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
