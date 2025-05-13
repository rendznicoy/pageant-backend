<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Candidate;
use App\Models\Category;
use App\Models\Score;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\PdfRequest\DownloadReportRequest;

class PdfReportController extends Controller
{
    public function download(DownloadReportRequest $request)
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        $judges = Judge::with('user')->where('event_id', $event->event_id)->get();
        $categories = Category::where('event_id', $event->event_id)->get();
        $candidates = Candidate::where('event_id', $event->event_id)->get();
        $scores = Score::where('event_id', $event->event_id)->get();

        $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'judges', 'categories', 'candidates', 'scores'));

        $filename = str_replace(' ', '_', strtolower($event->event_name)) . '_scores.pdf';

        return $pdf->download($filename);
    }

    public function preview(DownloadReportRequest $request)
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        $judges = Judge::with('user')->where('event_id', $event->event_id)->get();
        $categories = Category::where('event_id', $event->event_id)->get();
        $candidates = Candidate::where('event_id', $event->event_id)->get();
        $scores = Score::where('event_id', $event->event_id)->get();

        $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'judges', 'categories', 'candidates', 'scores'));

        return response($pdf->output(), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="' . str_replace(' ', '_', strtolower($event->event_name)) . '_scores_preview.pdf"');
    }
}