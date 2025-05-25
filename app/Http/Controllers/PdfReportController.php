<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Stage;
use App\Models\Score;
use App\Models\Judge;
use App\Models\Candidate;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Http\Requests\PdfRequest\DownloadReportRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PdfReportController extends Controller
{
    public function download($event_id)
    {
        try {
            $event = Event::where('event_id', $event_id)->firstOrFail();
            
            // Get final results data
            $finalResults = $this->getFinalResultsData($event_id);
            
            $pdf = Pdf::loadView('pdf.enhanced_event_scores', [
                'event' => $event,
                'results' => $finalResults['candidates'],
                'finalJudges' => $finalResults['judges']
            ]);
            
            $filename = str_replace(' ', '_', strtolower($event->event_name)) . '_scores.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF download failed', [
                'event_id' => $event_id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Failed to generate PDF'], 500);
        }
    }

    public function preview($event_id)
    {
        try {
            Log::info('PDF preview called for event:', ['event_id' => $event_id]);
            
            $event = Event::where('event_id', $event_id)->firstOrFail();
            
            // Get final results data
            $finalResults = $this->getFinalResultsData($event_id);
            
            $pdf = Pdf::loadView('pdf.enhanced_event_scores', [
                'event' => $event,
                'results' => $finalResults['candidates'],
                'finalJudges' => $finalResults['judges']
            ]);

            $pdfContent = $pdf->output();

            return response($pdfContent)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="event_preview.pdf"')
                ->header('Cache-Control', 'private, max-age=0, must-revalidate')
                ->header('Pragma', 'public');
        } catch (\Exception $e) {
            Log::error('PDF preview failed', [
                'event_id' => $event_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'message' => 'Failed to generate preview.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function getFinalResultsData($event_id)
    {
        // Get judges
        $judges = Judge::where('event_id', $event_id)
            ->with('user')
            ->get()
            ->map(function ($judge) {
                return [
                    'judge_id' => $judge->judge_id,
                    'name' => $judge->user->first_name . ' ' . $judge->user->last_name
                ];
            });

        // Get latest stage
        $latestStageId = DB::table('stages')
            ->where('event_id', $event_id)
            ->orderByDesc('created_at')
            ->value('stage_id');

        if (!$latestStageId) {
            return ['candidates' => [], 'judges' => $judges];
        }

        // Get final results with judge scores
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        $results = [];
        foreach ($candidates as $candidate) {
            $judgeScores = [];
            $totalScore = 0;
            $judgeCount = 0;

            foreach ($judges as $judge) {
                $avgScore = Score::where('event_id', $event_id)
                    ->where('candidate_id', $candidate->candidate_id)
                    ->where('judge_id', $judge['judge_id'])
                    ->where('stage_id', $latestStageId)
                    ->where('status', 'confirmed')
                    ->avg('score');

                if ($avgScore !== null) {
                    $judgeScores[$judge['judge_id']] = [
                        'score' => $avgScore,
                        'rank' => 0 // Will calculate ranks later
                    ];
                    $totalScore += $avgScore;
                    $judgeCount++;
                }
            }

            if ($judgeCount > 0) {
                $results[] = [
                    'candidate_id' => $candidate->candidate_id,
                    'candidate' => [
                        'candidate_number' => $candidate->candidate_number,
                        'first_name' => $candidate->first_name,
                        'last_name' => $candidate->last_name,
                        'team' => $candidate->team
                    ],
                    'sex' => $candidate->sex,
                    'mean_rating' => $totalScore / $judgeCount,
                    'judge_scores' => $judgeScores,
                    'overall_rank' => 0 // Will calculate later
                ];
            }
        }

        // Calculate ranks
        $maleResults = collect($results)->filter(fn($r) => strtolower($r['sex']) === 'm');
        $femaleResults = collect($results)->filter(fn($r) => strtolower($r['sex']) === 'f');

        $maleResults = $maleResults->sortByDesc('mean_rating')->values();
        $femaleResults = $femaleResults->sortByDesc('mean_rating')->values();

        $finalResults = [];
        
        // Assign ranks to males
        foreach ($maleResults as $index => $result) {
            $result['overall_rank'] = $index + 1;
            $finalResults[] = $result;
        }

        // Assign ranks to females
        foreach ($femaleResults as $index => $result) {
            $result['overall_rank'] = $index + 1;
            $finalResults[] = $result;
        }

        return [
            'candidates' => $finalResults,
            'judges' => $judges->toArray()
        ];
    }
}