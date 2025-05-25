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
        Log::info("Fetching partial results with mean rank", ['event_id' => $event_id, 'stage_id' => $stage_id]);
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);

        // Get all categories for this stage
        $categories = Category::where('stage_id', $stage_id)->get();

        // Fetch all active candidates for the event
        $candidates = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->get();

        // Get all judges for this event
        $judges = Judge::where('event_id', $event_id)->with('user')->get();

        $results = [];

        foreach ($candidates as $candidate) {
            $categoryScores = [];
            $weightedTotal = 0;
            $totalWeight = 0;

            foreach ($categories as $category) {
                // Get raw scores for this candidate in this category
                $rawScores = Score::where('scores.stage_id', $stage_id)
                    ->where('scores.event_id', $event_id)
                    ->where('scores.candidate_id', $candidate->candidate_id)
                    ->where('scores.category_id', $category->category_id)
                    ->where('scores.status', 'confirmed')
                    ->pluck('scores.score')
                    ->toArray();

                if (!empty($rawScores)) {
                    // Calculate category average
                    $categoryAverage = array_sum($rawScores) / count($rawScores);
                    $categoryScores[] = $categoryAverage;

                    // Apply category weight
                    $weightedScore = $categoryAverage * ($category->category_weight / 100);
                    $weightedTotal += $weightedScore;
                    $totalWeight += $category->category_weight;
                }
            }

            // Calculate Mean Rating
            $meanRating = $totalWeight > 0 ? ($weightedTotal / $totalWeight) * 100 : 0;

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
                'mean_rating' => round($meanRating, 2),
                'judge_ranks' => [],
            ];
        }

        // Calculate ranks per judge
        foreach ($judges as $judge) {
            $judgeResults = [];
            foreach ($results as $resultIndex => $result) {
                $judgeScores = Score::where('event_id', $event_id)
                    ->where('stage_id', $stage_id)
                    ->where('candidate_id', $result['candidate_id'])
                    ->where('judge_id', $judge->judge_id)
                    ->where('status', 'confirmed')
                    ->get();

                $judgeTotal = 0;
                if ($judgeScores->isNotEmpty()) {
                    foreach ($judgeScores as $score) {
                        $category = $categories->firstWhere('category_id', $score->category_id);
                        if ($category) {
                            $weightedScore = $score->score * ($category->category_weight / 100);
                            $judgeTotal += $weightedScore;
                        }
                    }
                }

                $judgeResults[] = [
                    'candidate_id' => $result['candidate_id'],
                    'score' => $judgeTotal,
                    'result_index' => $resultIndex
                ];
            }

            // Sort by score and assign ranks
            usort($judgeResults, function($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            foreach ($judgeResults as $rank => $judgeResult) {
                $results[$judgeResult['result_index']]['judge_ranks'][] = $rank + 1;
            }
        }

        // Calculate Mean Rank
        foreach ($results as &$result) {
            if (!empty($result['judge_ranks'])) {
                $result['mean_rank'] = array_sum($result['judge_ranks']) / count($result['judge_ranks']);
            } else {
                $result['mean_rank'] = 999;
            }
        }

        // Sort by Mean Rank, then by Mean Rating
        usort($results, function ($a, $b) {
            if ($a['mean_rank'] == $b['mean_rank']) {
                return $b['mean_rating'] <=> $a['mean_rating'];
            }
            return $a['mean_rank'] <=> $b['mean_rank'];
        });

        // Assign overall rank by sex
        $maleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'm');
        $femaleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'f');

        $maleIndex = 1;
        foreach ($maleResults as &$result) {
            $result['overall_rank'] = $maleIndex++;
        }

        $femaleIndex = 1;
        foreach ($femaleResults as &$result) {
            $result['overall_rank'] = $femaleIndex++;
        }

        $finalResults = array_merge($maleResults, $femaleResults);

        Log::info("Partial results with mean rank computed", [
            'stage_id' => $stage_id,
            'candidates' => count($finalResults),
        ]);

        return response()->json(['candidates' => $finalResults]);
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
                    ->where('candidates.is_active', true)
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
}