<?php

namespace App\Services;

use App\Models\Score;
use Illuminate\Support\Facades\Log;

class ScoreCalculationService
{
    /**
     * Calculate weighted score for a candidate from a specific judge
     * This ensures consistent calculation across final results and partial results
     */
    public static function calculateJudgeWeightedScore($event_id, $candidate_id, $judge_id, $categories, $stage_id = null)
    {
        $judgeWeightedTotal = 0;
        $validCategoryWeight = 0;
        $hasScores = false;

        foreach ($categories as $category) {
            $query = Score::where('event_id', $event_id)
                ->where('candidate_id', $candidate_id)
                ->where('judge_id', $judge_id)
                ->where('category_id', $category->category_id)
                ->where('status', 'confirmed');
                
            if ($stage_id) {
                $query->where('stage_id', $stage_id);
            }

            $score = $query->first();

            if ($score) {
                // Apply category weight
                $weightedScore = $score->score * ($category->category_weight / 100);
                $judgeWeightedTotal += $weightedScore;
                $validCategoryWeight += $category->category_weight;
                $hasScores = true;
                
                Log::debug("Score calculation detail", [
                    'event_id' => $event_id,
                    'candidate_id' => $candidate_id,
                    'judge_id' => $judge_id,
                    'category_id' => $category->category_id,
                    'raw_score' => $score->score,
                    'category_weight' => $category->category_weight,
                    'weighted_score' => $weightedScore,
                    'running_total' => $judgeWeightedTotal
                ]);
            }
        }

        if (!$hasScores || $validCategoryWeight <= 0) {
            return null;
        }

        // Normalize if not all categories were scored
        if ($validCategoryWeight < 100) {
            $judgeWeightedTotal = $judgeWeightedTotal * (100 / $validCategoryWeight);
            
            Log::debug("Score normalized", [
                'original_total' => $judgeWeightedTotal * ($validCategoryWeight / 100),
                'valid_weight' => $validCategoryWeight,
                'normalized_total' => $judgeWeightedTotal
            ]);
        }

        return $judgeWeightedTotal;
    }
}