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

class StageController extends Controller
{
    public function index(Request $request, $event_id)
    {
        $stages = Stage::where('event_id', $event_id)->with('categories')->get();
        return response()->json(StageResource::collection($stages));
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
            $stage->refresh(); // Refresh model to get latest database state
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
            // Delete all scores for categories in this stage
            $deletedScores = Score::where('stage_id', $stage_id)->delete();
            Log::info("Scores deleted for stage", [
                'stage_id' => $stage_id,
                'deleted_count' => $deletedScores,
            ]);

            // Reset all categories in this stage
            $updatedCategories = Category::where('stage_id', $stage_id)->update([
                'status' => 'pending',
                'current_candidate_id' => null,
            ]);
            Log::info("Categories reset for stage", [
                'stage_id' => $stage_id,
                'updated_count' => $updatedCategories,
            ]);

            // Reset stage status
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
            'top_candidates_count' => 'required|integer|min:1',
        ]);
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'finalized') {
            return response()->json(['message' => 'Stage is not finalized'], 400);
        }
        $topCandidatesCount = $request->input('top_candidates_count');
        $candidateCount = Candidate::where('event_id', $event_id)->where('is_active', true)->count();
        if ($topCandidatesCount > $candidateCount) {
            return response()->json(['message' => 'Top candidates count exceeds available candidates'], 400);
        }
        $stage->update(['top_candidates_count' => $topCandidatesCount]);
        $topCandidates = Score::where('stage_id', $stage_id)
            ->where('status', 'confirmed')
            ->groupBy('candidate_id')
            ->select('candidate_id', DB::raw('AVG(score) as average_score'))
            ->orderBy('average_score', 'desc')
            ->take($topCandidatesCount)
            ->pluck('candidate_id');
        Candidate::where('event_id', $event_id)
            ->whereNotIn('candidate_id', $topCandidates)
            ->update(['is_active' => false]);
        return response()->json(['message' => 'Top candidates selected successfully', 'top_candidates' => $topCandidates]);
    }

    public function partialResults($event_id, $stage_id)
    {
        $stage = Stage::where('event_id', $event_id)->findOrFail($stage_id);
        if ($stage->status !== 'finalized') {
            return response()->json(['message' => 'Stage is not finalized'], 400);
        }
        $results = Score::where('stage_id', $stage_id)
            ->where('status', 'confirmed')
            ->with(['candidate'])
            ->groupBy('candidate_id')
            ->select('candidate_id', DB::raw('AVG(score) as average_score'))
            ->orderBy('average_score', 'desc')
            ->get()
            ->map(function ($result) {
                return [
                    'candidate_id' => $result->candidate_id,
                    'candidate' => [
                        'first_name' => $result->candidate->first_name,
                        'last_name' => $result->candidate->last_name,
                    ],
                    'average_score' => $result->average_score,
                ];
            });
        return response()->json(['results' => $results]);
    }
}