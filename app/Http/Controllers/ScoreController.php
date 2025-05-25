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
        Log::info("Fetching final results with enhanced scoring", ['event_id' => $event_id]);

        // Find latest stage_id
        $latestStageId = DB::table('stages')
            ->where('event_id', $event_id)
            ->orderByDesc('created_at')
            ->value('stage_id');

        if (!$latestStageId) {
            return response()->json(['message' => 'No stages found for this event.'], 404);
        }

        // Get all judges for the event
        $judges = Judge::where('event_id', $event_id)->with('user')->get();
        
        // Get confirmed scores with individual judge details
        $scores = Score::where('scores.event_id', $event_id)
            ->where('scores.status', 'confirmed')
            ->where('scores.stage_id', $latestStageId)
            ->whereNotNull('scores.score')
            ->join('candidates', 'scores.candidate_id', '=', 'candidates.candidate_id')
            ->join('judges', 'scores.judge_id', '=', 'judges.judge_id')
            ->join('users', 'judges.user_id', '=', 'users.user_id')
            ->join('categories', 'scores.category_id', '=', 'categories.category_id')
            ->select(
                'scores.candidate_id',
                'scores.judge_id',
                'scores.score',
                'candidates.sex',
                'candidates.first_name',
                'candidates.last_name',
                'candidates.candidate_number',
                'candidates.team',
                'users.first_name as judge_first_name',
                'users.last_name as judge_last_name',
                'categories.category_weight'
            )
            ->get();

        // Group scores by candidate
        $candidateScores = $scores->groupBy('candidate_id');
        
        $results = [];
        
        foreach ($candidateScores as $candidateId => $candidateScoreGroup) {
            $firstScore = $candidateScoreGroup->first();
            
            // Calculate weighted average per judge
            $judgeScores = [];
            foreach ($judges as $judge) {
                $judgeScoreData = $candidateScoreGroup->where('judge_id', $judge->judge_id);
                if ($judgeScoreData->isNotEmpty()) {
                    $weightedSum = $judgeScoreData->sum(function($score) {
                        return $score->score * ($score->category_weight / 100);
                    });
                    $totalWeight = $judgeScoreData->sum(function($score) {
                        return $score->category_weight / 100;
                    });
                    $judgeScores[$judge->judge_id] = [
                        'score' => $totalWeight > 0 ? $weightedSum / $totalWeight : 0,
                        'judge_name' => $judge->user->first_name . ' ' . $judge->user->last_name
                    ];
                }
            }
            
            // Calculate mean rating
            $meanRating = count($judgeScores) > 0 ? 
                array_sum(array_column($judgeScores, 'score')) / count($judgeScores) : 0;
            
            $results[] = [
                'candidate_id' => $candidateId,
                'candidate' => [
                    'first_name' => $firstScore->first_name,
                    'last_name' => $firstScore->last_name,
                    'candidate_number' => $firstScore->candidate_number,
                    'team' => $firstScore->team,
                ],
                'sex' => $firstScore->sex,
                'judge_scores' => $judgeScores,
                'mean_rating' => round($meanRating, 2),
                'raw_average' => round($meanRating, 2), // Keep for compatibility
            ];
        }
        
        // Calculate ranks per judge (by sex)
        $maleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'm');
        $femaleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'f');
        
        // Calculate judge ranks for males
        foreach ($judges as $judge) {
            $maleScoresForJudge = array_map(function($result) use ($judge) {
                return [
                    'candidate_id' => $result['candidate_id'],
                    'score' => $result['judge_scores'][$judge->judge_id]['score'] ?? 0
                ];
            }, $maleResults);
            
            usort($maleScoresForJudge, fn($a, $b) => $b['score'] <=> $a['score']);
            
            foreach ($maleScoresForJudge as $rank => $scoreData) {
                $candidateIndex = array_search($scoreData['candidate_id'], array_column($results, 'candidate_id'));
                if ($candidateIndex !== false) {
                    $results[$candidateIndex]['judge_scores'][$judge->judge_id]['rank'] = $rank + 1;
                }
            }
        }
        
        // Calculate judge ranks for females
        foreach ($judges as $judge) {
            $femaleScoresForJudge = array_map(function($result) use ($judge) {
                return [
                    'candidate_id' => $result['candidate_id'],
                    'score' => $result['judge_scores'][$judge->judge_id]['score'] ?? 0
                ];
            }, $femaleResults);
            
            usort($femaleScoresForJudge, fn($a, $b) => $b['score'] <=> $a['score']);
            
            foreach ($femaleScoresForJudge as $rank => $scoreData) {
                $candidateIndex = array_search($scoreData['candidate_id'], array_column($results, 'candidate_id'));
                if ($candidateIndex !== false) {
                    $results[$candidateIndex]['judge_scores'][$judge->judge_id]['rank'] = $rank + 1;
                }
            }
        }
        
        // Calculate mean rank and overall rank
        foreach ($results as &$result) {
            $ranks = array_column($result['judge_scores'], 'rank');
            $result['mean_rank'] = count($ranks) > 0 ? array_sum($ranks) / count($ranks) : 0;
        }
        
        // Sort by sex and then by mean rating (desc) and mean rank (asc)
        $maleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'm');
        $femaleResults = array_filter($results, fn($r) => strtolower($r['sex']) === 'f');
        
        // Sort males
        usort($maleResults, function($a, $b) {
            if ($a['mean_rating'] === $b['mean_rating']) {
                return $a['mean_rank'] <=> $b['mean_rank']; // Lower mean rank is better
            }
            return $b['mean_rating'] <=> $a['mean_rating']; // Higher mean rating is better
        });
        
        // Sort females
        usort($femaleResults, function($a, $b) {
            if ($a['mean_rating'] === $b['mean_rating']) {
                return $a['mean_rank'] <=> $b['mean_rank']; // Lower mean rank is better
            }
            return $b['mean_rating'] <=> $a['mean_rating']; // Higher mean rating is better
        });
        
        // Assign overall ranks
        foreach ($maleResults as $index => &$result) {
            $result['overall_rank'] = $index + 1;
            $result['rank'] = $index + 1; // Keep for compatibility
        }
        
        foreach ($femaleResults as $index => &$result) {
            $result['overall_rank'] = $index + 1;
            $result['rank'] = $index + 1; // Keep for compatibility
        }
        
        $allResults = array_merge($maleResults, $femaleResults);
        
        Log::info("Enhanced final results computed", [
            'result_count' => count($allResults),
            'judges_count' => $judges->count(),
        ]);

        return response()->json([
            'candidates' => $allResults,
            'judges' => $judges->map(fn($j) => [
                'judge_id' => $j->judge_id,
                'name' => $j->user->first_name . ' ' . $j->user->last_name
            ])
        ]);
    }
}