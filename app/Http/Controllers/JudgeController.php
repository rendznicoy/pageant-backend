<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\JudgeRequest\StoreJudgeRequest;
use App\Http\Requests\JudgeRequest\DestroyJudgeRequest;
use App\Http\Requests\JudgeRequest\ShowJudgeRequest;
use App\Http\Requests\JudgeRequest\UpdateJudgeRequest;
use App\Http\Resources\JudgeResource;
use App\Models\Judge;

class JudgeController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;
        $eventId = $request->query('event_id');

        $query = Judge::with('user');

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        $judges = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $judges->items(),
            'meta' => [
                'current_page' => $judges->currentPage(),
                'per_page' => $judges->perPage(),
                'total' => $judges->total(),
                'last_page' => $judges->lastPage(),
            ]
        ], 200);
    }

    public function store(StoreJudgeRequest $request)
    {
        $validated = $request->validated();
        $judge = Judge::create($validated);

        return response()->json([
            'message' => 'Judge created successfully.',
            'data' => new JudgeResource($judge),
        ], 201);
    }

    public function show(ShowJudgeRequest $request)
    {
        $validated = $request->validated();

        $judge = Judge::where('judge_id', $validated['judge_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        return response()->json(new JudgeResource($judge), 200);
    }

    public function update(UpdateJudgeRequest $request) 
    {
        $validated = $request->validated();

        $judge = Judge::where('judge_id', $validated['judge_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        $judge->update($validated);

        return response()->json([
            'message' => 'Judge updated successfully.',
            'judge' => new JudgeResource($judge),
        ]);
    }

    public function destroy(DestroyJudgeRequest $request) 
    {
        $validated = $request->validated();

        $judge = Judge::where('judge_id', $validated['judge_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        $judge->delete();

        return response()->json(['message' => 'Judge deleted successfully.'], 204);
    }
}
