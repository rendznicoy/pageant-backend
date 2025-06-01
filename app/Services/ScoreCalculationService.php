<?php
// app/Services/ScoreCalculationService.php

namespace App\Services;

use App\Models\Score;
use App\Models\Category;
use App\Models\Judge;
use App\Models\Candidate;
use Illuminate\Support\Facades\Log;

class ScoreCalculationService
{
    /**
     * Calculate weighted score for a specific judge and candidate
     * Formula: Σ(category_score * category_weight/100)
     */
    public static function calculateJudgeWeightedScore($event_id, $candidate_id, $judge_id, $categories = null, $stage_id = null)
    {
        // Get categories if not provided
        if ($categories === null) {
            $query = Category::where('event_id', $event_id);
            if ($stage_id) {
                $query->where('stage_id', $stage_id);
            }
            $categories = $query->get();
        }

        $weightedTotal = 0;
        $hasScores = false;

        foreach ($categories as $category) {
            $scoreQuery = Score::where('event_id', $event_id)
                ->where('candidate_id', $candidate_id)
                ->where('category_id', $category->category_id)
                ->where('judge_id', $judge_id)
                ->where('status', 'confirmed');
            
            if ($stage_id) {
                $scoreQuery->where('stage_id', $stage_id);
            }
            
            $score = $scoreQuery->first();

            if ($score) {
                // Apply the correct weighted formula: score * (weight/100)
                $weightedScore = $score->score * ($category->category_weight / 100);
                $weightedTotal += $weightedScore;
                $hasScores = true;
            }
        }

        return $hasScores ? $weightedTotal : null;
    }

    /**
     * Calculate all judges' weighted scores for a candidate
     */
    public static function calculateCandidateJudgeRatings($event_id, $candidate_id, $categories = null, $stage_id = null)
    {
        $judges = Judge::where('event_id', $event_id)->get();
        $judgeRatings = [];

        foreach ($judges as $judge) {
            $weightedScore = self::calculateJudgeWeightedScore($event_id, $candidate_id, $judge->judge_id, $categories, $stage_id);
            
            if ($weightedScore !== null) {
                $judgeRatings[] = $weightedScore;
            }
        }

        return $judgeRatings;
    }

    /**
     * Calculate mean rating for a candidate
     */
    public static function calculateMeanRating($judgeRatings)
    {
        if (empty($judgeRatings)) {
            return null;
        }

        return array_sum($judgeRatings) / count($judgeRatings);
    }

    /**
     * Calculate mean rank for candidates within a sex group
     */
    public static function calculateMeanRanks($candidatesData, $sex)
    {
        $judges = [];
        $maxJudges = 0;

        // Determine the maximum number of judges
        foreach ($candidatesData as $candidate) {
            if (strtoupper($candidate['sex']) === strtoupper($sex)) {
                $judgeCount = count($candidate['judge_ratings'] ?? []);
                if ($judgeCount > $maxJudges) {
                    $maxJudges = $judgeCount;
                }
            }
        }

        // For each judge position, rank candidates of this sex
        for ($judgeIndex = 0; $judgeIndex < $maxJudges; $judgeIndex++) {
            $judgeRatings = [];
            
            foreach ($candidatesData as $candidate) {
                if (strtoupper($candidate['sex']) === strtoupper($sex) && 
                    isset($candidate['judge_ratings'][$judgeIndex])) {
                    $judgeRatings[] = [
                        'candidate_id' => $candidate['candidate_id'],
                        'rating' => $candidate['judge_ratings'][$judgeIndex]
                    ];
                }
            }

            if (empty($judgeRatings)) continue;

            // Sort by rating (descending) and assign ranks
            usort($judgeRatings, fn($a, $b) => $b['rating'] <=> $a['rating']);
            
            foreach ($judgeRatings as $rank => $judgeRating) {
                // Find the candidate in the original array and add the rank
                foreach ($candidatesData as $key => $candidate) {
                    if ($candidate['candidate_id'] === $judgeRating['candidate_id']) {
                        if (!isset($candidatesData[$key]['judge_ranks'])) {
                            $candidatesData[$key]['judge_ranks'] = [];
                        }
                        $candidatesData[$key]['judge_ranks'][] = $rank + 1; // 1-based ranking
                        break;
                    }
                }
            }
        }

        // Calculate mean rank for each candidate
        foreach ($candidatesData as $key => $candidate) {
            if (strtoupper($candidate['sex']) === strtoupper($sex)) {
                if (!empty($candidate['judge_ranks'])) {
                    $candidatesData[$key]['mean_rank'] = array_sum($candidate['judge_ranks']) / count($candidate['judge_ranks']);
                } else {
                    $candidatesData[$key]['mean_rank'] = 999; // High number for no ranks
                }
            }
        }

        return $candidatesData;
    }
}