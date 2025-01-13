<?php

namespace App\Http\Controllers;

use App\Events\ProcessStatusStarted;
use App\Http\Requests\ImageRequest;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;
use App\Models\Image;
use App\Jobs\ProcessImage;

class ImageController extends Controller
{
    public function index(): Response
    {
        $images = Image::latest()->paginate(10);

        $images->getCollection()->transform(function ($image) {
            $image->imageUrl = asset('storage/' . $image->image_url);
            return $image;
        });

        return Inertia::render('Images/Index', compact('images'));
    }

    public function store(ImageRequest $request)
    {
        $model = $request->validated()['model'];
        $quality = $request->validated()['quality'];
        $prompt = $request->validated()['prompt'];
        $style = $request->validated()['style'];
        $size = $request->validated()['size'];


        event(new ProcessStatusStarted(auth()->id(), 'Generating image'));
        // Dispatch job
        ProcessImage::dispatch($model, $prompt, $style, $size, $quality, auth()->id());

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
