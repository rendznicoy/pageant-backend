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
    public function index(Request $request)
    {
        $eventId = $request->query('event_id');
        $judgeId = $request->query('judge_id');
        $candidateId = $request->query('candidate_id');
        $categoryId = $request->query('category_id');

        $query = Score::with(['judge.user', 'candidate', 'category', 'event']);

        if ($eventId) $query->where('event_id', $eventId);
        if ($judgeId) $query->where('judge_id', $judgeId);
        if ($candidateId) $query->where('candidate_id', $candidateId);
        if ($categoryId) $query->where('category_id', $categoryId);

        $scores = $query->get();

        return response()->json($scores, 200);
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

        $score = Score::where('judge_id', $validated['judge_id'])
            ->where('candidate_id', $validated['candidate_id'])
            ->where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        $score->update(['score' => $validated['score']]);

        return response()->json([
            'message' => 'Score updated successfully.',
            'score' => new ScoreResource($score->load(['event', 'judge.user', 'candidate', 'category'])),
        ]);
    }

    public function destroy(DestroyScoreRequest $request)
    {
        $validated = $request->validated();

        $score = Score::where('event_id', $validated['event_id'])
            ->where('judge_id', $validated['judge_id'])
            ->where('candidate_id', $validated['candidate_id'])
            ->where('category_id', $validated['category_id'])
            ->firstOrFail();

        $score->delete();

        return response()->json(['message' => 'Score deleted successfully.'], 204);
    }
}
