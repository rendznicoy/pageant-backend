<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\ScoreRequest\StoreScoreRequest;
use App\Models\Score;
use App\Http\Resources\ScoreResource;
use App\Models\Event;

class ScoreController extends Controller
{
    public function index()
    {
        // Return all scores, grouped by event if needed
        $scores = Score::with(['event', 'judge.user', 'candidate', 'category'])->get();
        return ScoreResource::collection($scores);
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

        return new ScoreResource($score->load(['event', 'judge.user', 'candidate', 'category']));
    }
}
