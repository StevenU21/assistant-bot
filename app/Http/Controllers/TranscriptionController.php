<?php
namespace App\Http\Controllers;

use App\Http\Requests\TranscriptionRequest;
use Illuminate\Http\Request;
use App\Services\OpenAIService;
use App\Models\Transcription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class TranscriptionController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function index(): Response
    {
        $transcriptions = Transcription::latest()->paginate(10);
        return Inertia::render('Transcriptions/Index', compact('transcriptions'));
    }

    public function create(): Response
    {
        return Inertia::render('Transcriptions/Create');
    }

    public function store(TranscriptionRequest $request): RedirectResponse
    {
        $file = $request->file('audio');
        $language = $request->input('language') === 'auto' ? null : $request->input('language');

        $storedFilePath = $file->store('audios', 'public');
        $filePath = storage_path('app/public/' . $storedFilePath);
        $response = $this->openAIService->transcribe($filePath, $language);

        Transcription::create([
            'title' => $file->getClientOriginalName(),
            'content' => $response['text'],
            'language' => $language ?? $response['language'],
            'audio' => $storedFilePath,
            'slug' => Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME) . '-' . time()),
        ]);

        return redirect()->route('transcriptions.index')->with('success', 'Transcripción realizada correctamente.');
    }

    public function destroy(Transcription $transcription): RedirectResponse
    {
        $transcription->delete();
        return redirect()->route('transcriptions.index');
    }
}
