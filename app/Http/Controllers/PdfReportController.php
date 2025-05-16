<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Stage;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\PdfRequest\DownloadReportRequest;
use Illuminate\Support\Facades\DB;

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

        $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'stages'));

        $filename = str_replace(' ', '_', strtolower($event->event_name)) . '_scores.pdf';
        return $pdf->download($filename);
    }

    public function preview(DownloadReportRequest $request)
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

        $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'stages'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . str_replace(' ', '_', strtolower($event->event_name)) . '_scores_preview.pdf"');
    }
}