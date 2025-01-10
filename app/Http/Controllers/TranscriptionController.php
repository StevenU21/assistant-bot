<?php
namespace App\Http\Controllers;

use App\Http\Requests\TranscriptionRequest;
use App\Services\OpenAIService;
use App\Models\Transcription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;

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

        $transcriptions->getCollection()->transform(function ($transcription) {
            $transcription->audioUrl = asset('storage/' . $transcription->audio);
            return $transcription;
        });

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

        return redirect()->route('transcriptions.index');
    }

    public function download(Transcription $transcription)
    {
        $pdf = $this->generatePdf($transcription);

        $name = Str::slug($transcription->title, '-') . '.pdf';

        return $pdf->download('Transcription to ' . $transcription->language . ' ' . $name);
    }

    public function show(Transcription $transcription)
    {
        $pdf = $this->generatePdf($transcription);

        return $pdf->stream($transcription->title . '.pdf');
    }

    private function generatePdf(Transcription $transcription)
    {
        $data = [
            'title' => $transcription->title,
            'content' => $transcription->content,
            'language' => $transcription->language,
        ];

        return PDF::loadView('transcriptions.pdf', $data);
    }

    public function destroy(Transcription $transcription): RedirectResponse
    {
        Storage::disk('public')->delete($transcription->audio);

        $transcription->delete();

        return redirect()->route('transcriptions.index');
    }
}
