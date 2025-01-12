<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class ChatBotController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('ChatBot/Index');
    }
}
