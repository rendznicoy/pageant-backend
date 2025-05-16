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
            'score' => 'required|integer|min:0|max:100', // Changed min:1|max:10 to min:0|max:100
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
        if ($category->status !== 'active') {
            Log::warning("Category not active", ['category_id' => $request->category_id]);
            return response()->json(['message' => 'Category is not active'], 403);
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
            'score' => 'required|integer|min:0|max:100', // Changed min:1 to min:0|max:100
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
                DB::commit();
                broadcast(new ScoreConfirmed($request->event_id, $score->getKey(), $score))->toOthers();
                Log::info("Score confirmed successfully", [
                    'judge_id' => $judge->judge_id,
                    'user_id' => $user->user_id,
                    'score_id' => $score->getKey(),
                ]);
                return response()->json(['message' => 'Score confirmed successfully']);
            } else {
                $score->update([
                    'score' => $request->score,
                    'comments' => $request->comments,
                    'status' => 'temporary',
                    'updated_at' => now(),
                ]);
                DB::commit();
                Log::info("Score updated as temporary", [
                    'judge_id' => $judge->judge_id,
                    'user_id' => $user->user_id,
                ]);
                return response()->json(['message' => 'Score updated. Please resubmit for confirmation']);
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
}