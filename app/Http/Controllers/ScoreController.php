<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ScoreRequest\StoreScoreRequest;
use App\Http\Requests\ScoreRequest\ShowScoreRequest;
use App\Http\Requests\ScoreRequest\UpdateScoreRequest;
use App\Http\Requests\ScoreRequest\DestroyScoreRequest;
use App\Models\Score;
use App\Http\Resources\ScoreResource;
use App\Models\Event;
use App\Models\Category;
use App\Events\ScoreSubmitted;
use App\Events\ScoreConfirmed;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $score = Score::updateOrCreate(
            [
                'event_id' => $data['event_id'],
                'judge_id' => $data['judge_id'],
                'candidate_id' => $data['candidate_id'],
                'category_id' => $data['category_id'],
            ],
            [
                'score' => $data['score'],
                'stage_id' => $category->stage_id,
                'status' => 'confirmed',
                'comments' => $data['comments'] ?? null,
            ]
        );

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
        $event = Event::find($validated['event_id']);

        if (!$event || $event->status !== 'active') {
            return response()->json(['message' => 'Scores can only be updated for active events.'], 403);
        }

        $category = Category::find($validated['category_id']);
        $updatedRows = Score::where('judge_id', $validated['judge_id'])
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
            $score = Score::where('judge_id', $validated['judge_id'])
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
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'category_id' => 'required|exists:categories,category_id',
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'score' => 'required|numeric|min:1|max:10',
            'comments' => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user || !$user->user_id) {
            Log::error("No authenticated user or user_id in ScoreController::submit", [
                'user' => $user ? (array) $user : null,
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $judge = \App\Models\Judge::where('user_id', $user->user_id)->first();
        if (!$judge) {
            Log::error("No judge entry found for user in ScoreController::submit", [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        $category = Category::findOrFail($request->category_id);
        if ($category->status !== 'active' || $category->current_candidate_id !== $request->candidate_id) {
            Log::warning("Invalid category or candidate in ScoreController::submit", [
                'category_id' => $request->category_id,
                'candidate_id' => $request->candidate_id,
                'category_status' => $category->status,
                'current_candidate_id' => $category->current_candidate_id,
            ]);
            return response()->json(['message' => 'Invalid category or candidate'], 400);
        }

        $event = Event::findOrFail($request->event_id);
        if ($event->status !== 'active') {
            Log::warning("Event is not active in ScoreController::submit", [
                'event_id' => $request->event_id,
                'event_status' => $event->status,
            ]);
            return response()->json(['message' => 'Event is not active'], 403);
        }

        try {
            DB::beginTransaction();
            $score = Score::updateOrCreate(
                [
                    'event_id' => $request->event_id,
                    'judge_id' => $judge->judge_id,
                    'candidate_id' => $request->candidate_id,
                    'category_id' => $request->category_id,
                ],
                [
                    'score' => $request->score,
                    'stage_id' => $category->stage_id,
                    'status' => 'temporary',
                    'comments' => $request->comments,
                ]
            );
            DB::commit();
            broadcast(new ScoreSubmitted($request->event_id, $score->id, $score))->toOthers();
            Log::info("Score submitted successfully in ScoreController::submit", [
                'score_id' => $score->id ?? 'null',
                'judge_id' => $judge->judge_id,
                'candidate_id' => $request->candidate_id,
                'category_id' => $request->category_id,
                'score' => $request->score,
                'comments' => $request->comments,
            ]);
            return response()->json([
                'message' => 'Score submitted successfully. Please confirm.',
                'score' => new ScoreResource($score->load(['judge.user', 'candidate', 'category'])),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to submit score in ScoreController::submit", [
                'judge_id' => $judge->judge_id,
                'candidate_id' => $request->candidate_id,
                'category_id' => $request->category_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to submit score'], 500);
        }
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'category_id' => 'required|exists:categories,category_id',
            'candidate_id' => 'required|exists:candidates,candidate_id',
            'score' => 'required|numeric|min:1|max:10',
            'comments' => 'nullable|string',
            'confirm' => 'required|boolean',
        ]);

        $user = auth()->user();
        if (!$user || !$user->user_id) {
            Log::error("No authenticated user or user_id in ScoreController::confirm", [
                'user' => $user ? (array) $user : null,
            ]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $judge = \App\Models\Judge::where('user_id', $user->user_id)->first();
        if (!$judge) {
            Log::error("No judge entry found for user in ScoreController::confirm", [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        try {
            DB::beginTransaction();
            $score = Score::where('event_id', $request->event_id)
                ->where('judge_id', $judge->judge_id)
                ->where('candidate_id', $request->candidate_id)
                ->where('category_id', $request->category_id)
                ->where('status', 'temporary')
                ->firstOrFail();

            if ($request->confirm) {
                $score->update([
                    'score' => $request->score,
                    'comments' => $request->comments,
                    'status' => 'confirmed',
                ]);
                DB::commit();
                broadcast(new ScoreConfirmed($request->event_id, $score->id, $score))->toOthers();
                Log::info("Score confirmed successfully in ScoreController::confirm", [
                    'score_id' => $score->id ?? 'null',
                    'judge_id' => $judge->judge_id,
                    'candidate_id' => $request->candidate_id,
                    'category_id' => $request->category_id,
                    'score' => $request->score,
                    'comments' => $request->comments,
                ]);
                return response()->json(['message' => 'Score confirmed successfully']);
            } else {
                $score->update([
                    'score' => $request->score,
                    'comments' => $request->comments,
                    'status' => 'temporary',
                ]);
                DB::commit();
                Log::info("Score updated as temporary in ScoreController::confirm", [
                    'score_id' => $score->id ?? 'null',
                    'judge_id' => $judge->judge_id,
                    'candidate_id' => $request->candidate_id,
                    'category_id' => $request->category_id,
                    'score' => $request->score,
                    'comments' => $request->comments,
                ]);
                return response()->json(['message' => 'Score updated. Please resubmit for confirmation']);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to confirm score in ScoreController::confirm", [
                'judge_id' => $judge->judge_id,
                'candidate_id' => $request->candidate_id,
                'category_id' => $request->category_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Failed to confirm score'], 500);
        }
    }

    public function finalResults(Request $request, $event_id)
    {
        $event = Event::findOrFail($event_id);
        if ($event->status !== 'completed') {
            return response()->json(['message' => 'Final results are not available yet'], 403);
        }

        $results = Score::where('event_id', $event_id)
            ->where('status', 'confirmed')
            ->groupBy('candidate_id')
            ->selectRaw('candidate_id, AVG(score) as average_score')
            ->with(['candidate'])
            ->orderBy('average_score', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.final_results', ['results' => $results]);
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, "event_{$event_id}_final_results.pdf");
    }
}