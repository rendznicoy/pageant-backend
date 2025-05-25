<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ScoreRequest\StoreScoreRequest;
use App\Http\Requests\ScoreRequest\ShowScoreRequest;
use App\Http\Requests\ScoreRequest\UpdateScoreRequest;
use App\Http\Requests\ScoreRequest\DestroyScoreRequest;
use App\Http\Resources\ScoreResource;
use App\Models\Category;
use App\Models\Event;
use App\Models\Score;
use App\Models\Judge;
use App\Events\ScoreSubmitted;
use App\Events\ScoreConfirmed;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use App\Models\Candidate;
use App\Events\CandidateSet;
use App\Models\Stage;

class ScoreController extends Controller
{
    public function index(Request $request, $event_id)
    {
        $query = Score::with(['event', 'judge.user', 'candidate', 'category'])
            ->where('event_id', $event_id);

        if ($request->has('judge_id')) {
            $query->where('judge_id', $request->query('judge_id'));
        }

        if ($request->has('candidate_id')) {
            $query->where('candidate_id', $request->query('candidate_id'));
        }

        if ($request->has('category_id')) {
            $query->where('category_id', $request->query('category_id'));
        }

        $scores = $query->get();

        return response()->json(ScoreResource::collection($scores));
    }

    public function store(StoreScoreRequest $request)
    {
        $data = $request->validated();
        $user = auth()->user();
        $judge = \App\Models\Judge::where('user_id', $user->user_id)->first();

        if (!$judge) {
            Log::error("No judge entry found for user in ScoreController::store", [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        if ($data['judge_id'] !== $judge->judge_id) {
            Log::warning("Attempt to submit score for another judge in ScoreController::store", [
                'user_id' => $user->user_id,
                'requested_judge_id' => $data['judge_id'],
                'actual_judge_id' => $judge->judge_id,
            ]);
            return response()->json(['message' => 'Cannot submit score for another judge'], 403);
        }

        $event = Event::find($data['event_id']);
        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        if ($event->status === 'inactive') {
            return response()->json(['message' => 'Scoring is disabled. Event is inactive.'], 403);
        }

        if ($event->status === 'completed') {
            return response()->json(['message' => 'Scoring is closed. Event has already been completed.'], 403);
        }

        $category = Category::find($data['category_id']);
        $score = Score::create([
            'event_id' => $data['event_id'],
            'judge_id' => $judge->judge_id,
            'candidate_id' => $data['candidate_id'],
            'category_id' => $data['category_id'],
            'score' => $data['score'],
            'stage_id' => $category->stage_id,
            'status' => 'confirmed',
            'comments' => $data['comments'] ?? null,
        ]);

        return response()->json([
            'message' => 'Score submitted successfully.',
            'data' => new ScoreResource($score->load(['event', 'judge.user', 'candidate', 'category'])),
        ], 201);
    }

    public function show(ShowScoreRequest $request)
    {
        $validated = $request->validated();

        $score = Score::where('judge_id', $validated['judge_id'])
            ->where('candidate_id', $validated['candidate_id'])
            ->where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        return response()->json(new ScoreResource($score->load(['judge.user', 'candidate', 'category', 'event'])), 200);
    }

    public function update(UpdateScoreRequest $request)
    {
        $validated = $request->validated();
        $user = auth()->user();
        $judge = \App\Models\Judge::where('user_id', $user->user_id)->first();

        if (!$judge) {
            Log::error("No judge entry found for user in ScoreController::update", [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        if ($validated['judge_id'] !== $judge->judge_id) {
            Log::warning("Attempt to update score for another judge in ScoreController::update", [
                'user_id' => $user->user_id,
                'requested_judge_id' => $validated['judge_id'],
                'actual_judge_id' => $judge->judge_id,
            ]);
            return response()->json(['message' => 'Cannot update score for another judge'], 403);
        }

        $event = Event::find($validated['event_id']);
        if (!$event || $event->status !== 'active') {
            return response()->json(['message' => 'Scores can only be updated for active events.'], 403);
        }

        $category = Category::find($validated['category_id']);
        $updatedRows = Score::where('judge_id', $judge->judge_id)
            ->where('candidate_id', $validated['candidate_id'])
            ->where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->update([
                'score' => $validated['score'],
                'stage_id' => $category->stage_id,
                'status' => 'confirmed',
                'comments' => $validated['comments'] ?? null,
            ]);

        if ($updatedRows > 0) {
            $score = Score::where('judge_id', $judge->judge_id)
                ->where('candidate_id', $validated['candidate_id'])
                ->where('category_id', $validated['category_id'])
                ->where('event_id', $validated['event_id'])
                ->firstOrFail();

            return response()->json([
                'message' => 'Score updated successfully.',
                'score' => new ScoreResource($score->load(['event', 'judge.user', 'candidate', 'category'])),
            ]);
        }

        return response()->json(['message' => 'Score update failed.'], 500);
    }

    public function destroy(DestroyScoreRequest $request)
    {
        $validated = $request->validated();

        $deletedRows = Score::where('event_id', $validated['event_id'])
            ->where('judge_id', $validated['judge_id'])
            ->where('candidate_id', $validated['candidate_id'])
            ->where('category_id', $validated['category_id'])
            ->delete();

        if ($deletedRows > 0) {
            return response()->json(['message' => 'Score deleted successfully.'], 204);
        }

        return response()->json(['message' => 'Score deletion failed.'], 500);
    }

    public function submit(Request $request)
    {
        Log::info("Score submission attempt started", [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);

        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'category_id' => 'required|exists:categories,category_id',
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'comments' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user || !$user->user_id) {
            Log::error("No authenticated user", ['user' => $user]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $judge = Judge::where('user_id', $user->user_id)->where('event_id', $request->event_id)->first();
        if (!$judge) {
            Log::error("No judge entry for user", [
                'user_id' => $user->user_id,
                'event_id' => $request->event_id,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        $event = Event::findOrFail($request->event_id);

        // Add max score validation
        $category = Category::findOrFail($request->category_id);
        if ($request->score < 0 || $request->score > $category->max_score) {
            return response()->json([
                'message' => "Score must be between 0 and {$category->max_score} for this category."
            ], 422);
        }

        if ($event->status !== 'active') {
            Log::warning("Event not active", ['event_id' => $request->event_id]);
            return response()->json(['message' => 'Event is not active'], 403);
        }

        $category = Category::findOrFail($request->category_id);
        
        if ($request->score < 0 || $request->score > $category->max_score) {
            return response()->json([
                'message' => "Score must be between 0 and {$category->max_score} for this category."
            ], 422);
        }

        if ($category->current_candidate_id !== (int) $request->candidate_id) {
            Log::warning("Invalid candidate", [
                'category_id' => $request->category_id,
                'candidate_id' => $request->candidate_id,
                'current_candidate_id' => $category->current_candidate_id,
            ]);
            return response()->json(['message' => 'Invalid candidate'], 400);
        }

        try {
            DB::beginTransaction();
            $attributes = [
                'event_id' => $request->event_id,
                'judge_id' => $judge->judge_id,
                'candidate_id' => $request->candidate_id,
                'category_id' => $request->category_id,
            ];
            $values = [
                'score' => $request->score,
                'stage_id' => $category->stage_id,
                'status' => 'temporary',
                'comments' => $request->comments,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $score = Score::findByCompositeKey($attributes);
            if ($score) {
                $score->update($values);
                $wasCreated = false;
                $wasChanged = $score->wasChanged();
            } else {
                $score = Score::create(array_merge($attributes, $values));
                $wasCreated = true;
                $wasChanged = false;
            }

            Log::debug("Score operation executed", [
                'was_created' => $wasCreated,
                'was_changed' => $wasChanged,
                'attributes' => $score->getAttributes(),
            ]);
            DB::commit();
            broadcast(new ScoreSubmitted($request->event_id, $score->getKey(), $score))->toOthers();
            Log::info("Score submitted successfully", [
                'judge_id' => $judge->judge_id,
                'user_id' => $user->user_id,
                'score_id' => $score->getKey(),
            ]);
            return response()->json([
                'message' => 'Score submitted successfully. Please confirm.',
                'score' => new ScoreResource($score),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Score submission failed", [
                'user_id' => $user->user_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to submit score'], 500);
        }
    }

    public function confirm(Request $request)
    {
        Log::info("Score confirmation attempt started", [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
        ]);

        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'category_id' => 'required|exists:categories,category_id',
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'comments' => 'nullable|string',
            'confirm' => 'required|boolean',
        ]);

        $user = auth()->user();
        if (!$user || !$user->user_id) {
            Log::error("No authenticated user", ['user' => $user]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $judge = Judge::where('user_id', $user->user_id)->where('event_id', $request->event_id)->first();
        if (!$judge) {
            Log::error("No judge entry for user", [
                'user_id' => $user->user_id,
                'event_id' => $request->event_id,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        try {
            DB::beginTransaction();
            $score = Score::where([
                'event_id' => $request->event_id,
                'judge_id' => $judge->judge_id,
                'candidate_id' => $request->candidate_id,
                'category_id' => $request->category_id,
                'status' => 'temporary',
            ])->first();

            if (!$score) {
                Log::warning("No temporary score found", [
                    'judge_id' => $judge->judge_id,
                    'user_id' => $user->user_id,
                ]);
                return response()->json(['message' => 'No temporary score found'], 404);
            }

            if ($request->confirm) {
                $score->update([
                    'score' => $request->score,
                    'comments' => $request->comments,
                    'status' => 'confirmed',
                    'updated_at' => now(),
                ]);
            
                // ✅ Check if all judges have confirmed for this candidate and category
                $allConfirmed = Score::where('event_id', $request->event_id)
                    ->where('category_id', $request->category_id)
                    ->where('candidate_id', $request->candidate_id)
                    ->where('status', 'confirmed')
                    ->count() === Judge::where('event_id', $request->event_id)->count();
            
                    if ($allConfirmed) {
                        Log::info("All judges confirmed. Waiting for admin/tabulator to assign next candidate.", [
                            'event_id' => $request->event_id,
                            'category_id' => $request->category_id,
                        ]);
                    }
            
                DB::commit();
                broadcast(new ScoreConfirmed($request->event_id, $score->getKey(), $score))->toOthers();
            
                return response()->json(['message' => 'Score confirmed successfully']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Score confirmation failed", [
                'user_id' => $user->user_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to confirm score'], 500);
        }
    }

    public function export($event_id)
    {
        $scores = Score::with(['candidate', 'judge', 'category'])
            ->where('event_id', $event_id)
            ->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=event_{$event_id}_scores.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($scores) {
            $file = fopen('php://output', 'w');
            // CSV Header
            fputcsv($file, ['Candidate', 'Judge', 'Category', 'Score', 'Comments']);

            foreach ($scores as $score) {
                Log::debug("Export row", [
                    'candidate' => $score->candidate?->first_name,
                    'judge' => $score->judge?->user?->first_name,
                    'category' => $score->category?->category_name
                ]);
                
                fputcsv($file, [
                    $score->candidate?->first_name . ' ' . $score->candidate?->last_name,
                    $score->judge?->user?->first_name . ' ' . $score->judge?->user?->last_name,
                    $score->category?->category_name, // not ->name
                    $score->score,
                    $score->comments ?? 'No comment'
                ]);
            }                                  

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function finalResults($event_id)
    {
        Log::info("Fetching enhanced final results for event", ['event_id' => $event_id]);

        try {
            // Get event details for max score
            $event = Event::findOrFail($event_id);
            $maxScore = $event->max_score ?? 100;

            // Get latest stage
            $latestStage = Stage::where('event_id', $event_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if (!$latestStage) {
                return response()->json(['message' => 'No stages found for this event.'], 404);
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
                $totalWeight = 0;
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
                        // Step 2: Calculate category average (raw average as per requirement)
                        $categoryAverage = array_sum($rawScores) / count($rawScores);
                        $categoryScores[] = $categoryAverage;

                        // Step 3: Apply category weight to the average
                        $weightedScore = $categoryAverage * ($category->category_weight / 100);
                        $weightedTotal += $weightedScore;
                        $totalWeight += $category->category_weight;
                    }
                }

                // Step 4: Calculate Mean Rating (weighted average of all categories)
                $meanRating = $totalWeight > 0 ? ($weightedTotal / $totalWeight) * 100 : 0;

                // Step 5: Calculate individual judge ranks for this candidate
                foreach ($judges as $judge) {
                    $judgeScores = Score::where('event_id', $event_id)
                        ->where('candidate_id', $candidate->candidate_id)
                        ->where('judge_id', $judge->judge_id)
                        ->where('status', 'confirmed')
                        ->get();

                    if ($judgeScores->isNotEmpty()) {
                        $judgeTotal = 0;
                        foreach ($judgeScores as $score) {
                            $category = $categories->firstWhere('category_id', $score->category_id);
                            if ($category) {
                                $weightedScore = $score->score * ($category->category_weight / 100);
                                $judgeTotal += $weightedScore;
                            }
                        }
                        $judgeRanks[] = $judgeTotal;
                    }
                }

                // Step 6: Calculate Mean Rank (this will be calculated after we rank all candidates)
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
                    'judge_scores' => $judgeRanks,
                    'category_scores' => $categoryScores,
                ];
            }

            // Step 7: Calculate ranks per judge and then Mean Rank
            foreach ($judges as $judgeIndex => $judge) {
                // Get all candidates' scores for this judge and rank them
                $judgeResults = collect($results)->map(function ($result) use ($judgeIndex) {
                    return [
                        'candidate_id' => $result['candidate_id'],
                        'score' => $result['judge_scores'][$judgeIndex] ?? 0,
                    ];
                })->sortByDesc('score')->values();

                // Assign ranks
                foreach ($judgeResults as $rank => $judgeResult) {
                    $resultIndex = collect($results)->search(function ($item) use ($judgeResult) {
                        return $item['candidate_id'] === $judgeResult['candidate_id'];
                    });
                    
                    if ($resultIndex !== false) {
                        $results[$resultIndex]['judge_ranks'][] = $rank + 1;
                    }
                }
            }

            // Step 8: Calculate Mean Rank for each candidate
            foreach ($results as &$result) {
                if (!empty($result['judge_ranks'])) {
                    $result['mean_rank'] = array_sum($result['judge_ranks']) / count($result['judge_ranks']);
                } else {
                    $result['mean_rank'] = 999; // High number for no scores
                }
            }

            // Step 9: Final ranking - Sort by Mean Rank (ascending), then by Mean Rating (descending)
            usort($results, function ($a, $b) {
                if ($a['mean_rank'] == $b['mean_rank']) {
                    return $b['mean_rating'] <=> $a['mean_rating']; // Higher rating wins
                }
                return $a['mean_rank'] <=> $b['mean_rank']; // Lower rank wins
            });

            // Step 10: Assign overall rank by sex
            $maleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'm');
            $femaleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'f');

            // Assign ranks within each sex
            foreach ([$maleResults, $femaleResults] as $sexResults) {
                foreach ($sexResults as $index => &$result) {
                    $result['overall_rank'] = $index + 1;
                }
            }

            // Combine results back
            $finalResults = array_merge($maleResults, $femaleResults);

            Log::info("Enhanced final results computed", [
                'result_count' => count($finalResults),
                'max_score' => $maxScore,
                'categories_count' => $categories->count(),
            ]);

            return response()->json([
                'candidates' => $finalResults,
                'judges' => $judges->map(fn($j) => [
                    'judge_id' => $j->judge_id,
                    'name' => $j->user->first_name . ' ' . $j->user->last_name,
                ]),
                'max_score' => $maxScore,
                'categories' => $categories->map(fn($c) => [
                    'category_id' => $c->category_id,
                    'name' => $c->category_name,
                    'weight' => $c->category_weight,
                    'max_score' => $c->max_score,
                ]),
            ]);

        } catch (\Exception $e) {
            Log::error("Error computing enhanced final results", [
                'event_id' => $event_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'message' => 'Failed to compute results',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}