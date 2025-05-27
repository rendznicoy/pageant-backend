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

    // In app/Http/Controllers/StageController.php - partialResults method
    public function partialResults($event_id, $stage_id)
    {
        Log::info("Fetching partial results", ['event_id' => $event_id, 'stage_id' => $stage_id]);
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);

        // Get active candidates
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        // Get confirmed scores for the stage
        $scores = Score::where('scores.stage_id', $stage_id)
            ->where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->whereNotNull('scores.score')
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->join('judges', 'scores.judge_id', '=', 'judges.judge_id')
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                DB::raw('SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) as weighted_score')
            )
            ->groupBy('scores.candidate_id', 'scores.judge_id', 'candidates.sex', 'candidates.first_name', 'candidates.last_name', 'candidates.candidate_number')
            ->get();

        $results = [];
        
        // Process each sex separately
        foreach (['M', 'F'] as $sex) {
            $sexCandidates = $candidates->where('sex', $sex);
            $sexScores = $scores->where('sex', $sex);
            
            foreach ($sexCandidates as $candidate) {
                $candidateScores = $sexScores->where('candidate_id', $candidate->candidate_id);
                
                if ($candidateScores->isEmpty()) {
                    $results[] = [
                        'candidate_id' => $candidate->candidate_id,
                        'candidate' => [
                            'first_name' => $candidate->first_name,
                            'last_name' => $candidate->last_name,
                            'candidate_number' => $candidate->candidate_number,
                        ],
                        'sex' => $candidate->sex,
                        'raw_average' => null,
                        'mean_rank' => null,
                        'rank' => null,
                    ];
                    continue;
                }

                // Calculate Mean Rating (average of weighted scores)
                $weightedScores = $candidateScores->pluck('weighted_score')->map(fn($score) => (float)$score);
                $meanRating = $weightedScores->avg();

                // Calculate Mean Rank
                $judges = $candidateScores->pluck('judge_id')->unique();
                $rankSum = 0;
                $rankCount = 0;

                foreach ($judges as $judgeId) {
                    // Get all candidates' scores for this judge and sex
                    $judgeScores = $sexScores->where('judge_id', $judgeId)
                        ->sortByDesc('weighted_score')
                        ->values();
                    
                    // Find this candidate's rank for this judge
                    $rank = $judgeScores->search(function($item) use ($candidate) {
                        return $item->candidate_id == $candidate->candidate_id;
                    });
                    
                    if ($rank !== false) {
                        $rankSum += ($rank + 1); // Convert to 1-based ranking
                        $rankCount++;
                    }
                }

                $meanRank = $rankCount > 0 ? round($rankSum / $rankCount, 2) : null;

                $results[] = [
                    'candidate_id' => $candidate->candidate_id,
                    'candidate' => [
                        'first_name' => $candidate->first_name,
                        'last_name' => $candidate->last_name,
                        'candidate_number' => $candidate->candidate_number,
                    ],
                    'sex' => $candidate->sex,
                    'raw_average' => $meanRating ? round($meanRating, 2) : null,
                    'mean_rank' => $meanRank,
                    'rank' => null, // Will be set below
                ];
            }
        }

        // Sort and rank separately by sex
        foreach (['M', 'F'] as $sex) {
            $sexResults = collect($results)->where('sex', $sex)
                ->filter(fn($r) => $r['raw_average'] !== null)
                ->sortBy([
                    ['raw_average', 'desc'],
                    ['mean_rank', 'asc'] // Lower mean rank is better for tiebreaking
                ])
                ->values();

            $rank = 1;
            $prevScore = null;
            $prevMeanRank = null;

            foreach ($sexResults as $index => $result) {
                if ($prevScore !== $result['raw_average'] || $prevMeanRank !== $result['mean_rank']) {
                    $rank = $index + 1;
                }
                
                // Update the rank in the main results array
                $resultIndex = array_search($result['candidate_id'], array_column($results, 'candidate_id'));
                if ($resultIndex !== false) {
                    $results[$resultIndex]['rank'] = $rank;
                }
                
                $prevScore = $result['raw_average'];
                $prevMeanRank = $result['mean_rank'];
            }
        }

        // Sort final results by sex (M first) and then by rank
        usort($results, function($a, $b) {
            if ($a['sex'] !== $b['sex']) {
                return $a['sex'] === 'M' ? -1 : 1;
            }
            if ($a['rank'] === null && $b['rank'] === null) return 0;
            if ($a['rank'] === null) return 1;
            if ($b['rank'] === null) return -1;
            return $a['rank'] <=> $b['rank'];
        });

        Log::info("Partial results computed with proper ranking", [
            'stage_id' => $stage_id,
            'results_count' => count($results),
        ]);

        return $this->permanentPartialResults($event_id, $stage_id);
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
                        DB::raw('ROUND(AVG(scores.score), 2) as category_average') // Add ROUND here
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
                
                // Add ranks and ensure 2 decimal places
                $males = $males->map(function ($item, $index) {
                    $item->rank = $index + 1;
                    $item->category_average = number_format((float)$item->category_average, 2, '.', '');
                    return $item;
                });
                
                $females = $females->map(function ($item, $index) {
                    $item->rank = $index + 1;
                    $item->category_average = number_format((float)$item->category_average, 2, '.', '');
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

        // Get only ACTIVE candidates
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        if ($candidates->isEmpty()) {
            return response()->json([
                'candidates' => [],
                'males' => [],
                'females' => []
            ]);
        }

        // Get confirmed scores for active candidates only
        $scores = Score::where('scores.stage_id', $stage_id)
            ->where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->whereNotNull('scores.score')
            ->where('scores.score', '>=', 0)
            ->where('scores.score', '<=', 100)
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->where('candidates.is_active', true)
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                'candidates.team',
                'candidates.is_active',
                DB::raw('SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) as weighted_score')
            )
            ->groupBy('scores.candidate_id', 'scores.judge_id', 'candidates.sex', 'candidates.first_name', 'candidates.last_name', 'candidates.candidate_number', 'candidates.team', 'candidates.is_active')
            ->havingRaw('weighted_score IS NOT NULL AND weighted_score >= 0')
            ->get();

        // Process results for each sex separately
        $processedResults = [];
        
        foreach (['M', 'F'] as $sex) {
            $sexCandidates = $candidates->where('sex', $sex);
            $sexScores = $scores->where('sex', $sex);
            
            foreach ($sexCandidates as $candidate) {
                $candidateScores = $sexScores->where('candidate_id', $candidate->candidate_id);
                
                if ($candidateScores->isEmpty()) {
                    continue; // Skip candidates with no scores
                }

                // Calculate mean rating
                $weightedScores = $candidateScores->pluck('weighted_score')->map(function($score) {
                    return (float)$score;
                })->toArray();
                
                $meanRating = count($weightedScores) > 0 ? array_sum($weightedScores) / count($weightedScores) : null;

                // Calculate mean rank for this sex only
                $judges = $candidateScores->pluck('judge_id')->unique();
                $rankSum = 0;
                $rankCount = 0;

                foreach ($judges as $judgeId) {
                    // Get this judge's scores for this sex, sorted by weighted_score
                    $judgeScoresForSex = $sexScores->where('judge_id', $judgeId)
                        ->sortByDesc('weighted_score')
                        ->values();
                    
                    // Find this candidate's position in the judge's ranking
                    $position = null;
                    foreach ($judgeScoresForSex as $index => $score) {
                        if ($score->candidate_id == $candidate->candidate_id) {
                            $position = $index + 1; // 1-based ranking
                            break;
                        }
                    }
                    
                    if ($position !== null) {
                        $rankSum += $position;
                        $rankCount++;
                    }
                }

                $meanRank = $rankCount > 0 ? $rankSum / $rankCount : null;

                $processedResults[] = [
                    'candidate_id' => $candidate->candidate_id,
                    'candidate' => [
                        'first_name' => $candidate->first_name,
                        'last_name' => $candidate->last_name,
                        'candidate_number' => $candidate->candidate_number,
                        'team' => $candidate->team,
                        'is_active' => $candidate->is_active,
                    ],
                    'sex' => $candidate->sex,
                    'raw_average' => $meanRating ? number_format($meanRating, 2, '.', '') : null,
                    'mean_rating' => $meanRating ? number_format($meanRating, 2, '.', '') : null,
                    'mean_rank' => $meanRank ? number_format($meanRank, 2, '.', '') : null,
                ];
            }
        }

        // Separate and rank by sex
        $males = collect($processedResults)->where('sex', 'M')->sortBy([
            ['mean_rank', 'asc'],
            ['mean_rating', 'desc']
        ])->values();

        $females = collect($processedResults)->where('sex', 'F')->sortBy([
            ['mean_rank', 'asc'], 
            ['mean_rating', 'desc']
        ])->values();

        // Add ranks within each sex
        $rankedMales = $males->map(function($candidate, $index) {
            $candidate['rank'] = $index + 1;
            return $candidate;
        });

        $rankedFemales = $females->map(function($candidate, $index) {
            $candidate['rank'] = $index + 1;
            return $candidate;
        });

        Log::info("Active partial results computed", [
            'stage_id' => $stage_id,
            'males_count' => $rankedMales->count(),
            'females_count' => $rankedFemales->count(),
        ]);

        return response()->json([
            'candidates' => $rankedMales->concat($rankedFemales)->toArray(),
            'males' => $rankedMales->toArray(),
            'females' => $rankedFemales->toArray(),
        ]);
    }

    public function permanentPartialResults($event_id, $stage_id)
    {
        Log::info("Fetching permanent partial results", ['event_id' => $event_id, 'stage_id' => $stage_id]);
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);

        // Get ALL candidates from the event
        $candidates = Candidate::where('event_id', $event_id)->get();

        if ($candidates->isEmpty()) {
            return response()->json([
                'candidates' => [],
                'males' => [],
                'females' => []
            ]);
        }

        // Get confirmed scores for this specific stage
        $scores = Score::where('scores.stage_id', $stage_id)
            ->where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->whereNotNull('scores.score')
            ->where('scores.score', '>=', 0)
            ->where('scores.score', '<=', 100)
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                'candidates.team',
                'candidates.is_active',
                DB::raw('SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) as weighted_score')
            )
            ->groupBy('scores.candidate_id', 'scores.judge_id', 'candidates.sex', 'candidates.first_name', 'candidates.last_name', 'candidates.candidate_number', 'candidates.team', 'candidates.is_active')
            ->havingRaw('weighted_score IS NOT NULL AND weighted_score >= 0')
            ->get();

        // Process results for each sex separately
        $processedResults = [];
        
        foreach (['M', 'F'] as $sex) {
            $sexCandidates = $candidates->where('sex', $sex);
            $sexScores = $scores->where('sex', $sex);
            
            foreach ($sexCandidates as $candidate) {
                $candidateScores = $sexScores->where('candidate_id', $candidate->candidate_id);
                
                if ($candidateScores->isEmpty()) {
                    continue; // Skip candidates with no scores
                }

                // Calculate mean rating
                $weightedScores = $candidateScores->pluck('weighted_score')->map(function($score) {
                    return (float)$score;
                })->toArray();
                
                $meanRating = count($weightedScores) > 0 ? array_sum($weightedScores) / count($weightedScores) : null;

                // Calculate mean rank for this sex only
                $judges = $candidateScores->pluck('judge_id')->unique();
                $rankSum = 0;
                $rankCount = 0;

                foreach ($judges as $judgeId) {
                    // Get this judge's scores for this sex, sorted by weighted_score
                    $judgeScoresForSex = $sexScores->where('judge_id', $judgeId)
                        ->sortByDesc('weighted_score')
                        ->values();
                    
                    // Find this candidate's position in the judge's ranking
                    $position = null;
                    foreach ($judgeScoresForSex as $index => $score) {
                        if ($score->candidate_id == $candidate->candidate_id) {
                            $position = $index + 1; // 1-based ranking
                            break;
                        }
                    }
                    
                    if ($position !== null) {
                        $rankSum += $position;
                        $rankCount++;
                    }
                }

                $meanRank = $rankCount > 0 ? $rankSum / $rankCount : null;

                $processedResults[] = [
                    'candidate_id' => $candidate->candidate_id,
                    'candidate' => [
                        'first_name' => $candidate->first_name,
                        'last_name' => $candidate->last_name,
                        'candidate_number' => $candidate->candidate_number,
                        'team' => $candidate->team,
                        'is_active' => $candidate->is_active,
                    ],
                    'sex' => $candidate->sex,
                    'raw_average' => $meanRating ? number_format($meanRating, 2, '.', '') : null,
                    'mean_rating' => $meanRating ? number_format($meanRating, 2, '.', '') : null,
                    'mean_rank' => $meanRank ? number_format($meanRank, 2, '.', '') : null,
                ];
            }
        }

        // Separate and rank by sex
        $males = collect($processedResults)->where('sex', 'M')->sortBy([
            ['mean_rank', 'asc'],
            ['mean_rating', 'desc']
        ])->values();

        $females = collect($processedResults)->where('sex', 'F')->sortBy([
            ['mean_rank', 'asc'], 
            ['mean_rating', 'desc']
        ])->values();

        // Add ranks within each sex
        $rankedMales = $males->map(function($candidate, $index) {
            $candidate['rank'] = $index + 1;
            return $candidate;
        });

        $rankedFemales = $females->map(function($candidate, $index) {
            $candidate['rank'] = $index + 1;
            return $candidate;
        });

        Log::info("Permanent partial results computed", [
            'stage_id' => $stage_id,
            'males_count' => $rankedMales->count(),
            'females_count' => $rankedFemales->count(),
        ]);

        return response()->json([
            'candidates' => $rankedMales->concat($rankedFemales)->toArray(),
            'males' => $rankedMales->toArray(),
            'females' => $rankedFemales->toArray(),
        ]);
    }

    public function activePartialResults($event_id, $stage_id)
    {
        Log::info("Fetching active partial results", ['event_id' => $event_id, 'stage_id' => $stage_id]);
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);

        // Get only ACTIVE candidates
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        // Get confirmed scores for active candidates only
        $scores = Score::where('scores.stage_id', $stage_id)
            ->where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->whereNotNull('scores.score')
            ->where('scores.score', '>=', 0)
            ->where('scores.score', '<=', 100)
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->join('judges', 'scores.judge_id', '=', 'judges.judge_id')
            ->where('candidates.is_active', true) // Only active candidates
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                'candidates.team',
                'candidates.is_active',
                DB::raw('SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) as weighted_score'),
                DB::raw('ROW_NUMBER() OVER (PARTITION BY scores.judge_id ORDER BY SUM(CAST(scores.score AS DECIMAL(10,2)) * COALESCE(categories.category_weight, 0) / 100) DESC) as judge_rank')
            )
            ->groupBy('scores.candidate_id', 'scores.judge_id', 'candidates.sex', 'candidates.first_name', 'candidates.last_name', 'candidates.candidate_number', 'candidates.team', 'candidates.is_active')
            ->havingRaw('weighted_score IS NOT NULL AND weighted_score >= 0')
            ->get();

        // Group scores by candidate_id
        $scoresByCandidate = $scores->groupBy('candidate_id');

        $results = [];
        foreach ($candidates as $candidate) {
            $candidateScores = $scoresByCandidate->get($candidate->candidate_id, collect());
            
            if ($candidateScores->isEmpty()) {
                continue; // Skip candidates with no confirmed scores
            }

            $weightedScores = $candidateScores->pluck('weighted_score')->map(fn($score) => (float)$score)->toArray();
            $judgeRanks = $candidateScores->pluck('judge_rank')->map(fn($rank) => (int)$rank)->toArray();
            
            $judgeCount = count($weightedScores);
            $rawAverage = $judgeCount > 0 ? array_sum($weightedScores) / $judgeCount : null;
            $meanRank = $judgeCount > 0 ? array_sum($judgeRanks) / $judgeCount : null;

            $results[] = [
                'candidate_id' => $candidate->candidate_id,
                'candidate' => [
                    'first_name' => $candidate->first_name,
                    'last_name' => $candidate->last_name,
                    'candidate_number' => $candidate->candidate_number,
                    'team' => $candidate->team,
                    'is_active' => $candidate->is_active,
                ],
                'sex' => $candidate->sex,
                'raw_average' => $rawAverage ? number_format($rawAverage, 2, '.', '') : null,
                'mean_rating' => $rawAverage ? number_format($rawAverage, 2, '.', '') : null,
                'mean_rank' => $meanRank ? number_format($meanRank, 2, '.', '') : null,
            ];
        }

        // Separate by sex and rank within each sex
        $males = collect($results)->filter(fn($r) => strtolower($r['sex']) === 'm')->sortBy([
            ['mean_rank', 'asc'],
            ['mean_rating', 'desc']
        ])->values();

        $females = collect($results)->filter(fn($r) => strtolower($r['sex']) === 'f')->sortBy([
            ['mean_rank', 'asc'],
            ['mean_rating', 'desc']
        ])->values();

        // Assign ranks within each sex
        $rankedMales = $males->map(function($candidate, $index) {
            $candidate['rank'] = $index + 1;
            return $candidate;
        });

        $rankedFemales = $females->map(function($candidate, $index) {
            $candidate['rank'] = $index + 1;
            return $candidate;
        });

        Log::info("Active partial results computed", [
            'stage_id' => $stage_id,
            'males_count' => $rankedMales->count(),
            'females_count' => $rankedFemales->count(),
        ]);

        return response()->json([
            'candidates' => $rankedMales->concat($rankedFemales)->toArray(),
            'males' => $rankedMales->toArray(),
            'females' => $rankedFemales->toArray(),
        ]);
    }
}