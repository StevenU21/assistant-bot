<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageRequest;
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

        // Dispatch job
        ProcessImage::dispatch($model, $prompt, $style, $size,$quality, auth()->id());

        return redirect()->route('images.index');
    }
}
