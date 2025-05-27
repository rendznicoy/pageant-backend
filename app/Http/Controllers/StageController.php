<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\StageRequest\StoreStageRequest;
use App\Http\Requests\StageRequest\DestroyStageRequest;
use App\Http\Requests\StageRequest\ShowStageRequest;
use App\Http\Requests\StageRequest\UpdateStageRequest;
use App\Http\Resources\StageResource;
use App\Models\Stage;
use App\Models\Category;
use App\Models\Score;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use App\Events\StageStatusUpdated;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use App\Models\Judge;

class StageController extends Controller
{
    public function index(Request $request, $event_id)
    {
        Log::info('StageController@index called', ['event_id' => $event_id]);
        $stages = Stage::where('event_id', $event_id)->with('categories')->get();
        Log::info('Returning stages:', ['count' => $stages->count()]);
        return response()->json([
            'data' => StageResource::collection($stages)
        ]);
    }

    public function store(StoreStageRequest $request)
    {
        $validated = $request->validated();
        $stage = Stage::create(array_merge($validated, ['status' => 'pending']));
        return response()->json([
            'message' => 'Stage created successfully.',
            'data' => new StageResource($stage),
        ], 201);
    }

    public function show(ShowStageRequest $request)
    {
        $validated = $request->validated();
        $stage = Stage::where('stage_id', $validated['stage_id'])
            ->where('event_id', $validated['event_id'])
            ->with('categories')
            ->firstOrFail();
        return response()->json(new StageResource($stage), 200);
    }

    public function update(UpdateStageRequest $request)
    {
        $validated = $request->validated();
        $stage = Stage::where('stage_id', $validated['stage_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();
        $stage->update($validated);
        return response()->json([
            'message' => 'Stage updated successfully.',
            'stage' => new StageResource($stage),
        ]);
    }

    public function destroy(DestroyStageRequest $request)
    {
        $validated = $request->validated();
        $stage = Stage::where('stage_id', $validated['stage_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();
        $stage->delete();
        return response()->json(['message' => 'Stage deleted successfully.'], 204);
    }

    public function start(Request $request, $event_id, $stage_id)
    {
        Log::info("StageController::start called", [
            'event_id' => $event_id,
            'stage_id' => $stage_id,
        ]);

        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'pending') {
            Log::warning("Stage is not pending", ['stage_status' => $stage->status]);
            return response()->json(['message' => 'Stage is not pending'], 400);
        }

        $previousStage = Stage::where('event_id', $event_id)
            ->where('stage_id', '<', $stage_id)
            ->orderBy('stage_id', 'desc')
            ->first();
        if ($previousStage && $previousStage->status !== 'finalized') {
            Log::warning("Previous stage is not finalized", ['previous_stage_id' => $previousStage->stage_id]);
            return response()->json(['message' => 'Previous stage is not finalized'], 400);
        }

        try {
            $updated = $stage->update(['status' => 'active']);
            $stage->refresh();
            Log::info("Stage update attempted", [
                'stage_id' => $stage_id,
                'updated' => $updated,
                'new_status' => $stage->status,
            ]);

            if (!$updated || $stage->status !== 'active') {
                Log::error("Failed to update stage status to active", ['stage_id' => $stage_id]);
                return response()->json(['message' => 'Failed to update stage status'], 500);
            }

            broadcast(new StageStatusUpdated($stage_id, 'active', $event_id))->toOthers();
            return response()->json(['message' => 'Stage started successfully']);
        } catch (\Exception $e) {
            Log::error("Exception in StageController::start", [
                'stage_id' => $stage_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function finalize(Request $request, $event_id, $stage_id)
    {
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'active') {
            return response()->json(['message' => 'Stage is not active'], 400);
        }
        $activeCategories = Category::where('stage_id', $stage_id)
            ->where('status', '!=', 'finalized')
            ->exists();
        if ($activeCategories) {
            return response()->json(['message' => 'Not all categories are finalized'], 400);
        }
        $stage->update(['status' => 'finalized']);
        broadcast(new StageStatusUpdated($stage_id, 'finalized', $event_id))->toOthers();
        return response()->json(['message' => 'Stage finalized successfully']);
    }

    public function reset(Request $request, $event_id, $stage_id)
    {
        Log::info("StageController::reset called", [
            'event_id' => $event_id,
            'stage_id' => $stage_id,
        ]);

        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'active') {
            Log::warning("Stage is not active", ['stage_status' => $stage->status]);
            return response()->json(['message' => 'Stage is not active'], 400);
        }

        try {
            $deletedScores = Score::where('stage_id', $stage_id)->delete();
            Log::info("Scores deleted for stage", [
                'stage_id' => $stage_id,
                'deleted_count' => $deletedScores,
            ]);

            $updatedCategories = Category::where('stage_id', $stage_id)->update([
                'status' => 'pending',
                'current_candidate_id' => null,
            ]);
            Log::info("Categories reset for stage", [
                'stage_id' => $stage_id,
                'updated_count' => $updatedCategories,
            ]);

            $updated = $stage->update(['status' => 'pending']);
            $stage->refresh();
            Log::info("Stage reset attempted", [
                'stage_id' => $stage_id,
                'updated' => $updated,
                'new_status' => $stage->status,
            ]);

            if (!$updated || $stage->status !== 'pending') {
                Log::error("Failed to reset stage status to pending", ['stage_id' => $stage_id]);
                return response()->json(['message' => 'Failed to reset stage status'], 500);
            }

            broadcast(new StageStatusUpdated($stage_id, 'pending', $event_id))->toOthers();
            return response()->json(['message' => 'Stage reset successfully']);
        } catch (\Exception $e) {
            Log::error("Exception in StageController::reset", [
                'stage_id' => $stage_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function selectTopCandidates(Request $request, $event_id, $stage_id)
    {
        $request->validate([
            'top_candidates_count' => [
                'required',
                'integer',
                'min:2',
                function ($attribute, $value, $fail) {
                    if ($value % 2 !== 0) {
                        $fail('The :attribute must be an even number.');
                    }
                },
            ],
        ]);
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'finalized') {
            return response()->json(['message' => 'Stage is not finalized'], 400);
        }
        $topCandidatesCount = $request->input('top_candidates_count');
        $halfCount = (int)($topCandidatesCount / 2);

        // Validate candidate counts by sex
        $maleCount = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->where('sex', 'M')
            ->count();
        $femaleCount = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->where('sex', 'F')
            ->count();
        if ($maleCount < $halfCount || $femaleCount < $halfCount) {
            return response()->json([
                'message' => "Not enough candidates (need at least $halfCount males and $halfCount females)",
            ], 400);
        }

        // Get top male candidates
        $topMales = Score::where('scores.stage_id', $stage_id)
            ->where('scores.status', 'confirmed')
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->where('candidates.sex', 'M')
            ->groupBy('scores.candidate_id')
            ->select('scores.candidate_id', DB::raw('AVG(scores.score) as average_score'))
            ->orderBy('average_score', 'desc')
            ->take($halfCount)
            ->pluck('candidate_id');

        // Get top female candidates
        $topFemales = Score::where('scores.stage_id', $stage_id)
            ->where('scores.status', 'confirmed')
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->where('candidates.sex', 'F')
            ->groupBy('scores.candidate_id')
            ->select('scores.candidate_id', DB::raw('AVG(scores.score) as average_score'))
            ->orderBy('average_score', 'desc')
            ->take($halfCount)
            ->pluck('candidate_id');

        // Combine top candidates
        $topCandidates = $topMales->merge($topFemales);

        // Update candidate active status
        Candidate::where('event_id', $event_id)
            ->whereNotIn('candidate_id', $topCandidates)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Persist top_candidates_count
        $stage->update(['top_candidates_count' => $topCandidatesCount]);

        Log::info("Top candidates selected", [
            'event_id' => $event_id,
            'stage_id' => $stage_id,
            'top_candidates_count' => $topCandidatesCount,
            'male_candidates' => $topMales->toArray(),
            'female_candidates' => $topFemales->toArray(),
        ]);

        return response()->json([
            'message' => 'Top candidates selected successfully',
            'top_candidates' => $topCandidates->toArray(),
            'top_candidates_count' => $topCandidatesCount,
        ]);
    }

    public function resetTopCandidates(Request $request, $event_id, $stage_id)
    {
        Log::info("StageController::resetTopCandidates called", [
            'event_id' => $event_id,
            'stage_id' => $stage_id,
        ]);

        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'finalized') {
            Log::warning("Stage is not finalized", ['stage_status' => $stage->status]);
            return response()->json(['message' => 'Stage is not finalized'], 400);
        }

        try {
            // Reset is_active to true for all candidates in the event
            $updatedCount = Candidate::where('event_id', $event_id)
                ->update(['is_active' => true]);

            // Clear top_candidates_count
            $stage->update(['top_candidates_count' => null]);

            Log::info("Candidate active statuses reset", [
                'event_id' => $event_id,
                'stage_id' => $stage_id,
                'updated_count' => $updatedCount,
            ]);

            return response()->json([
                'message' => 'Top candidates selection reset successfully',
                'updated_count' => $updatedCount,
            ]);
        } catch (\Exception $e) {
            Log::error("Exception in StageController::resetTopCandidates", [
                'stage_id' => $stage_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function partialResults($event_id, $stage_id)
    {
        Log::info("Fetching historical partial results", ['event_id' => $event_id, 'stage_id' => $stage_id]);
        
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);

        // Get all candidates who have confirmed scores for this specific stage
        // This gives us the historical snapshot of who was eligible for this stage
        $candidatesWithScores = Score::where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->where('scores.stage_id', $stage_id)
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->select('candidates.*')
            ->distinct()
            ->get();

        // Get confirmed scores for these candidates in this stage
        $scores = Score::where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->where('scores.stage_id', $stage_id)
            ->whereNotNull('scores.score')
            ->whereIn('scores.candidate_id', $candidatesWithScores->pluck('candidate_id'))
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                'candidates.is_active', // Include current active status for reference
                DB::raw('SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) as weighted_score')
            )
            ->groupBy(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                'candidates.is_active'
            )
            ->havingRaw('weighted_score IS NOT NULL AND weighted_score >= 0')
            ->get();

        // Group scores by candidate_id for fast lookup
        $scoresByCandidate = $scores->groupBy('candidate_id');

        $results = [];
        foreach ($candidatesWithScores as $candidate) {
            $candidateScores = $scoresByCandidate->get($candidate->candidate_id, collect());
            $weightedScores = $candidateScores->pluck('weighted_score')->map(fn($score) => (float)$score)->toArray();
            $judgeCount = count($weightedScores);
            $rawAverage = $judgeCount > 0 ? array_sum($weightedScores) / $judgeCount : null;

            $results[] = [
                'candidate_id' => $candidate->candidate_id,
                'candidate' => [
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'candidate_number' => $candidate->candidate_number,
                    'is_active' => $candidate->is_active, // Current active status
                ],
                'sex' => $candidate->sex,
                'raw_average' => isset($rawAverage) ? round($rawAverage, 2) : null,
            ];
        }

        // Sort by raw_average (nulls last)
        usort($results, function($a, $b) {
            if (is_null($a['raw_average'])) return 1;
            if (is_null($b['raw_average'])) return -1;
            return $b['raw_average'] <=> $a['raw_average'];
        });

        // Assign rank (nulls have no rank)
        $rank = 0;
        $previousScore = null;
        foreach ($results as $index => &$result) {
            if ($result['raw_average'] !== null && ($previousScore === null || $previousScore != $result['raw_average'])) {
                $rank = $index + 1;
            }
            $result['rank'] = $result['raw_average'] !== null ? $rank : null;
            $previousScore = $result['raw_average'];
        }
        unset($result);

        Log::info("Historical partial results response", [
            'stage_id' => $stage_id,
            'total_candidates' => count($results),
            'candidates_with_scores' => $candidatesWithScores->count(),
        ]);

        return response()->json(['candidates' => $results]);
    }

    public function categoryResults($event_id, $stage_id)
    {
        try {
            $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
            
            // Get categories for this stage
            $categories = Category::where('stage_id', $stage_id)->get();
            
            $categoryResults = [];
            
            foreach ($categories as $category) {
                // Get average scores per candidate for this category
                $scores = Score::where('scores.category_id', $category->category_id)
                    ->where('scores.status', 'confirmed')
                    ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
                    ->select(
                        'scores.candidate_id',
                        'candidates.first_name',
                        'candidates.last_name',
                        'candidates.candidate_number',
                        'candidates.sex',
                        DB::raw('AVG(scores.score) as category_average')
                    )
                    ->groupBy(
                        'scores.candidate_id',
                        'candidates.first_name',
                        'candidates.last_name',
                        'candidates.candidate_number',
                        'candidates.sex'
                    )
                    ->orderBy('category_average', 'desc')
                    ->get();

                // Separate by gender and add ranks
                $males = $scores->where('sex', 'M')->values();
                $females = $scores->where('sex', 'F')->values();
                
                // Add ranks
                $males = $males->map(function ($item, $index) {
                    $item->rank = $index + 1;
                    return $item;
                });
                
                $females = $females->map(function ($item, $index) {
                    $item->rank = $index + 1;
                    return $item;
                });

                $categoryResults[] = [
                    'category_id' => $category->category_id,
                    'category_name' => $category->category_name,
                    'max_score' => $category->max_score,
                    'weight' => $category->category_weight,
                    'males' => $males,
                    'females' => $females
                ];
            }

            return response()->json([
                'stage' => [
                    'stage_id' => $stage->stage_id,
                    'stage_name' => $stage->stage_name
                ],
                'categories' => $categoryResults
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching category results', [
                'event_id' => $event_id,
                'stage_id' => $stage_id,
                'error' => $e->getMessage()
            ]);
            return response()->json(['message' => 'Failed to fetch category results'], 500);
        }
    }

    // Add this new method to StageController.php
    public function partialResultsActive($event_id, $stage_id)
    {
        Log::info("Fetching active-only partial results", ['event_id' => $event_id, 'stage_id' => $stage_id]);
        
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);

        // Only get currently active candidates
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        // Get confirmed scores only from this stage for active candidates
        $scores = Score::where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->where('scores.stage_id', $stage_id)
            ->whereNotNull('scores.score')
            ->whereIn('scores.candidate_id', $candidates->pluck('candidate_id'))
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                DB::raw('SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) as weighted_score')
            )
            ->groupBy(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number'
            )
            ->havingRaw('weighted_score IS NOT NULL AND weighted_score >= 0')
            ->get();

        // Group scores by candidate_id for fast lookup
        $scoresByCandidate = $scores->groupBy('candidate_id');

        $results = [];
        foreach ($candidates as $candidate) {
            $candidateScores = $scoresByCandidate->get($candidate->candidate_id, collect());
            $weightedScores = $candidateScores->pluck('weighted_score')->map(fn($score) => (float)$score)->toArray();
            $judgeCount = count($weightedScores);
            $rawAverage = $judgeCount > 0 ? array_sum($weightedScores) / $judgeCount : null;

            $results[] = [
                'candidate_id' => $candidate->candidate_id,
                'candidate' => [
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'candidate_number' => $candidate->candidate_number,
                    'is_active' => $candidate->is_active,
                ],
                'sex' => $candidate->sex,
                'raw_average' => isset($rawAverage) ? round($rawAverage, 2) : null,
            ];
        }

        // Sort by raw_average (nulls last)
        usort($results, function($a, $b) {
            if (is_null($a['raw_average'])) return 1;
            if (is_null($b['raw_average'])) return -1;
            return $b['raw_average'] <=> $a['raw_average'];
        });

        // Assign rank (nulls have no rank)
        $rank = 0;
        $previousScore = null;
        foreach ($results as $index => &$result) {
            if ($result['raw_average'] !== null && ($previousScore === null || $previousScore != $result['raw_average'])) {
                $rank = $index + 1;
            }
            $result['rank'] = $result['raw_average'] !== null ? $rank : null;
            $previousScore = $result['raw_average'];
        }
        unset($result);

        return response()->json(['candidates' => $results]);
    }
}