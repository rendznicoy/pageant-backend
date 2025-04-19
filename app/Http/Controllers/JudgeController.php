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
    public function index($event_id)
    {
        $judges = Judge::with('user')->where('event_id', $event_id)->get();

        return response()->json(JudgeResource::collection($judges));
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
