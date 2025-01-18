<?php

namespace App\Http\Controllers;

use App\Events\ProcessStatusStarted;
use App\Http\Requests\ImageRequest;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Image;
use App\Jobs\ProcessImage;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller
{
    use AuthorizesRequests;
    public function index(): Response
    {
        $user = Auth::user()->load('user_request');
        $images = Image::with('user')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        $images->getCollection()->transform(function ($image) {
            $image->imageUrl = asset('storage/' . $image->image_url);
            return $image;
        });

        return Inertia::render('Images/Index', compact('images'));
    }

    public function store(ImageRequest $request)
    {
        $this->authorize('makeRequest', auth()->user());

        $prompt = $request->validated()['prompt'];
        $style = $request->validated()['style'];
        $size = $request->validated()['size'];
        $model = $request->validated()['model'];
        $quality = $request->validated()['quality'];

        event(new ProcessStatusStarted(auth()->id(), 'Generating image'));
        // Dispatch job
        ProcessImage::dispatch($prompt, $style, $size, $model, $quality, auth()->id());

        return redirect()->route('images.index');
    }

    public function download($id)
    {
        $image = Image::findOrFail($id);

        return Storage::disk('public')->download($image->image_url);
    }

    public function destroy($id)
    {
        $image = Image::findOrFail($id);
        Storage::disk('public')->delete($image->image_url);
        $image->delete();

        return redirect()->route('images.index');
    }
}
