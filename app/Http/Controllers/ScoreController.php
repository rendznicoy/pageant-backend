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

        // Fetch the event directly using event_id
        $event = Event::find($data['event_id']);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        // Apply status-based scoring rules
        if ($event->status === 'inactive') {
            return response()->json(['message' => 'Scoring is disabled. Event is inactive.'], 403);
        }

        if ($event->status === 'completed') {
            return response()->json(['message' => 'Scoring is closed. Event has already been completed.'], 403);
        }

        // Create or update the score (composite key includes event_id now)
        $score = Score::updateOrCreate(
            [
                'event_id' => $data['event_id'],
                'judge_id' => $data['judge_id'],
                'candidate_id' => $data['candidate_id'],
                'category_id' => $data['category_id'],
            ],
            ['score' => $data['score']]
        );

        return response()->json([
            'message' => 'Score submitted successfully.',
            'data' => new ScoreResource($score->load(['event', 'judge.user', 'candidate', 'category'])),
        ], 201);
    }

    public function show(ShowScoreRequest $request, $id) 
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

        $updatedRows = Score::where('judge_id', $validated['judge_id'])
            ->where('candidate_id', $validated['candidate_id'])
            ->where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->update(['score' => $validated['score']]);

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
        } else {
            return response()->json(['message' => 'Score update failed.'], 500); // Or handle this as appropriate
        }
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
        } else {
            return response()->json(['message' => 'Score deletion failed.'], 500); // Or handle this as appropriate
        }
    }
}
