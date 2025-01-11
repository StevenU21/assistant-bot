<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class TranslationController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Translations/Index');
    }
}
