<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Judge;
use App\Models\Candidate;
use App\Models\Category;
use App\Models\Score;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfReportController extends Controller
{
    public function eventScores($event_id)
    {
        $event = Event::findOrFail($event_id);
        $judges = Judge::with('user')->where('event_id', $event_id)->get();
        $candidates = Candidate::where('event_id', $event_id)->get();
        $categories = Category::where('event_id', $event_id)->get();
        $scores = Score::where('event_id', $event_id)->get();

        $pdf = Pdf::loadView('pdf.event_scores', compact('event', 'judges', 'candidates', 'categories', 'scores'));
        return $pdf->download("{$event->event_name}_scores.pdf");
    }
}
