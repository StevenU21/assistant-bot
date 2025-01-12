<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;
use App\Models\Image;

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
}
