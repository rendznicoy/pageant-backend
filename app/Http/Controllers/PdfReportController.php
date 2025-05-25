<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Stage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\PdfRequest\DownloadReportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdfReportController extends Controller
{
    public function download(DownloadReportRequest $request)
    {
        $validated = $request->validated();
        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        // Get enhanced final results
        $finalResultsResponse = app(ScoreController::class)->finalResults($validated['event_id']);
        $finalResultsData = $finalResultsResponse->getData(true);
        
        $results = $finalResultsData['candidates'] ?? [];
        $finalJudges = $finalResultsData['judges'] ?? [];

        $pdf = Pdf::loadView('pdf.enhanced_event_scores', compact('event', 'results', 'finalJudges'))
            ->setPaper('a4', 'landscape');

        $filename = str_replace(' ', '_', strtolower($event->event_name)) . '_enhanced_scores.pdf';
        return $pdf->download($filename);
    }

    public function preview(DownloadReportRequest $request)
    {
        try {
            $event_id = $request->event_id;
            $validated = $request->validated();
            Log::info('event_id received:', ['event_id' => $validated['event_id']]);
            
            $event = Event::where('event_id', $event_id)->firstOrFail();

            // Get enhanced final results
            $finalResultsResponse = app(ScoreController::class)->finalResults($event_id);
            $finalResultsData = $finalResultsResponse->getData(true);
            
            $results = $finalResultsData['candidates'] ?? [];
            $finalJudges = $finalResultsData['judges'] ?? [];

            $pdf = Pdf::loadView('pdf.enhanced_event_scores', compact('event', 'results', 'finalJudges'))
                ->setPaper('a4', 'landscape');

            $pdfContent = $pdf->output();

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="enhanced_event_preview.pdf"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Throwable $e) {
            Log::error('Enhanced PDF preview failed', ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to generate enhanced preview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}