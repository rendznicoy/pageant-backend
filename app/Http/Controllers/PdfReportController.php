<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Stage;
use App\Models\Score;
use App\Models\Judge;
use App\Models\Candidate;
use App\Models\Category;
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
        // Get event details for dynamic max score
        $event = Event::findOrFail($event_id);
        $globalMaxScore = $event->global_max_score ?? 100;

        // Get latest stage
        $latestStage = Stage::where('event_id', $event_id)
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$latestStage) {
            return ['candidates' => [], 'judges' => []];
        }

        // Get all categories for the latest stage with their weights
        $categories = Category::where('stage_id', $latestStage->stage_id)
            ->where('event_id', $event_id)
            ->get();

        // Get all active candidates
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        // Get all judges for this event
        $judges = Judge::where('event_id', $event_id)->with('user')->get();

        $results = [];

        foreach ($candidates as $candidate) {
            $categoryScores = [];
            $weightedTotal = 0;
            $judgeRanks = [];

            foreach ($categories as $category) {
                // Step 1: Get raw scores for this candidate in this category
                $rawScores = Score::where('event_id', $event_id)
                    ->where('candidate_id', $candidate->candidate_id)
                    ->where('category_id', $category->category_id)
                    ->where('status', 'confirmed')
                    ->pluck('score')
                    ->toArray();

                if (!empty($rawScores)) {
                    // Step 2: Calculate category average
                    $categoryAverage = array_sum($rawScores) / count($rawScores);
                    $categoryScores[] = $categoryAverage;

                    // Step 3: Apply category weight using dynamic globalMaxScore
                    $weightedScore = $categoryAverage * ($category->category_weight / $globalMaxScore);
                    $weightedTotal += $weightedScore;
                }
            }

            // Step 4: Calculate Mean Rating (sum of all weighted category scores)
            $meanRating = $weightedTotal;

            $results[] = [
                'candidate_id' => $candidate->candidate_id,
                'candidate' => [
                    'candidate_number' => $candidate->candidate_number,
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'team' => $candidate->team
                ],
                'sex' => $candidate->sex,
                'mean_rating' => round($meanRating, 2),
                'judge_scores' => [],
                'category_scores' => $categoryScores,
            ];
        }

        // Step 5: Calculate individual judge scores and ranks
        $judgeData = [];
        foreach ($judges as $judge) {
            $judgeData[] = [
                'judge_id' => $judge->judge_id,
                'name' => $judge->user->first_name . ' ' . $judge->user->last_name
            ];

            // Calculate each candidate's total weighted score for this judge
            $judgeResults = [];
            foreach ($results as &$result) {
                $judgeScores = Score::where('event_id', $event_id)
                    ->where('candidate_id', $result['candidate_id'])
                    ->where('judge_id', $judge->judge_id)
                    ->where('status', 'confirmed')
                    ->get();

                $judgeTotal = 0;
                if ($judgeScores->isNotEmpty()) {
                    foreach ($judgeScores as $score) {
                        $category = $categories->firstWhere('category_id', $score->category_id);
                        if ($category) {
                            // ✅ FIX: Use globalMaxScore instead of normalization factor
                            $weightedScore = $score->score * ($category->category_weight / $globalMaxScore);
                            $judgeTotal += $weightedScore;
                        }
                    }
                }

                $judgeResults[] = [
                    'candidate_id' => $result['candidate_id'],
                    'score' => $judgeTotal
                ];
            }

            // Sort by score (descending) and assign ranks
            usort($judgeResults, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            // Assign ranks and store in results
            foreach ($judgeResults as $rank => $judgeResult) {
                $resultIndex = array_search($judgeResult['candidate_id'], array_column($results, 'candidate_id'));
                if ($resultIndex !== false) {
                    $results[$resultIndex]['judge_scores'][$judge->judge_id] = [
                        'score' => round($judgeResult['score'], 2),
                        'rank' => $rank + 1
                    ];
                }
            }
        }

        // Step 6: Calculate Mean Rank for each candidate
        foreach ($results as &$result) {
            $ranks = [];
            foreach ($result['judge_scores'] as $judgeScore) {
                $ranks[] = $judgeScore['rank'];
            }
            
            if (!empty($ranks)) {
                $result['mean_rank'] = array_sum($ranks) / count($ranks);
            } else {
                $result['mean_rank'] = 999; // High number for no scores
            }
        }

        // Step 7: Final ranking - Sort by Mean Rank (ascending), then by Mean Rating (descending)
        usort($results, function ($a, $b) {
            if ($a['mean_rank'] == $b['mean_rank']) {
                return $b['mean_rating'] <=> $a['mean_rating']; // Higher rating wins
            }
            return $a['mean_rank'] <=> $b['mean_rank']; // Lower rank wins
        });

        // Step 8: Assign overall rank by sex
        $maleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'm');
        $femaleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'f');

        // Assign ranks within each sex
        $maleIndex = 1;
        foreach ($maleResults as &$result) {
            $result['overall_rank'] = $maleIndex++;
        }

        $femaleIndex = 1;
        foreach ($femaleResults as &$result) {
            $result['overall_rank'] = $femaleIndex++;
        }

        // Combine results back
        $finalResults = array_merge($maleResults, $femaleResults);

        Log::info("PDF final results computed with correct weighted scoring", [
            'event_id' => $event_id,
            'global_max_score' => $globalMaxScore,
            'results_count' => count($finalResults),
        ]);

        return [
            'candidates' => $finalResults,
            'judges' => $judgeData
        ];
    }
}