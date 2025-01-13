<?php
namespace App\Http\Controllers;

use App\Events\ProcessStatusStarted;
use App\Http\Requests\TranscriptionRequest;
use App\Models\Transcription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Storage;
use App\Jobs\ProcessTranscription;

class TranscriptionController extends Controller
{
    public function index(): Response
    {
        $userId = auth()->id();
        $transcriptions = Transcription::with('user')
            ->where('user_id', $userId)
            ->latest()
            ->paginate(10);

        $transcriptions->getCollection()->transform(function ($transcription) {
            $transcription->audioUrl = asset('storage/' . $transcription->audio);
            return $transcription;
        });

        return Inertia::render('Transcriptions/Index', compact('transcriptions'));
    }

    public function store(TranscriptionRequest $request): RedirectResponse
    {
        $file = $request->file('audio');
        $language = $request->input('language');

        $storedFilePath = $file->store('audios', 'public');
        $fileName = $file->getClientOriginalName();

        event(new ProcessStatusStarted(auth()->id(), 'Generating transcription'));
        // Despachar el trabajo en cola (usar ruta relativa)
        ProcessTranscription::dispatch($storedFilePath, $fileName, $language, auth()->id());

        // Redirigir con un mensaje de éxito
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
