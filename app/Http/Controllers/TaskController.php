<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::latest()->get();
        return Inertia::render('Tasks/Index', compact('tasks'));
    }
}
    