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
        // Fetch stages and their results
        $stages = Stage::where('event_id', $event->event_id)
            ->get()
            ->map(function ($stage) {
                $stage->results = DB::table('scores')
                    ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
                    ->join('categories', 'scores.category_id', '=', 'categories.category_id')
                    ->where('scores.stage_id', $stage->stage_id)
                    ->where('scores.status', 'confirmed')
                    ->where('categories.stage_id', $stage->stage_id)
                    ->groupBy(
                        'scores.candidate_id',
                        'candidates.first_name',
                        'candidates.last_name',
                        'candidates.candidate_number',
                        'candidates.sex'
                    )
                    ->select(
                        'scores.candidate_id',
                        'candidates.first_name',
                        'candidates.last_name',
                        'candidates.candidate_number',
                        'candidates.sex',
                        DB::raw('
                            SUM(scores.score * categories.category_weight / categories.max_score) / 
                            SUM(categories.category_weight / categories.max_score) as raw_average
                        ')
                    )
                    ->orderBy('raw_average', 'desc')
                    ->get();
                return $stage;
            });

            $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'stages'))
            ->setPaper('a4', 'landscape'); // Add landscape orientation

            $filename = str_replace(' ', '_', strtolower($event->event_name)) . '_scores.pdf';
            return $pdf->download($filename);
    }

    public function preview(DownloadReportRequest $request)
    {
        try {
            $event_id = $request->event_id; // comes from merged route param
            $validated = $request->validated();
            Log::info('event_id received:', ['event_id' => $validated['event_id']]);
           // Use the path variable directly
           $event = Event::where('event_id', $event_id)->firstOrFail();

            $stages = Stage::where('event_id', $event->event_id)
                ->get()
                ->map(function ($stage) {
                    $stage->results = DB::table('scores')
                        ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
                        ->join('categories', 'scores.category_id', '=', 'categories.category_id')
                        ->where('scores.stage_id', $stage->stage_id)
                        ->where('scores.status', 'confirmed')
                        ->where('categories.stage_id', $stage->stage_id)
                        ->groupBy(
                            'scores.candidate_id',
                            'candidates.first_name',
                            'candidates.last_name',
                            'candidates.candidate_number',
                            'candidates.sex'
                        )
                        ->select(
                            'scores.candidate_id',
                            'candidates.first_name',
                            'candidates.last_name',
                            'candidates.candidate_number',
                            'candidates.sex',
                            DB::raw('
                                SUM(scores.score * categories.category_weight / categories.max_score) / 
                                SUM(categories.category_weight / categories.max_score) as raw_average
                            ')
                        )
                        ->orderBy('raw_average', 'desc')
                        ->get();
                    return $stage;
                });

                $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'stages'))
                ->setPaper('a4', 'landscape');

            $pdfContent = $pdf->output();

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="event_preview.pdf"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Throwable $e) {
            Log::error('PDF preview failed', ['exception' => $e]);
            return response()->json([
                'message' => 'Failed to generate preview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}