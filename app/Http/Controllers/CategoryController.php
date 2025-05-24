<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest\StoreCategoryRequest;
use App\Http\Requests\CategoryRequest\DestroyCategoryRequest;
use App\Http\Requests\CategoryRequest\ShowCategoryRequest;
use App\Http\Requests\CategoryRequest\UpdateCategoryRequest;
use App\Http\Requests\CategoryRequest\PendingScoresForAllRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Candidate;
use App\Models\Score;
use App\Events\CategoryStatusUpdated;
use App\Events\CandidateSet;
use App\Models\Stage;
use Illuminate\Support\Facades\Log;
use App\Models\Judge;

class CategoryController extends Controller
{
    public function index(Request $request, $event_id)
    {
        $categories = Category::where('event_id', $event_id)->with('stage')->get();
        return response()->json(CategoryResource::collection($categories));
    }

    public function store(StoreCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::create(array_merge($validated, [
            'status' => 'pending',
            'current_candidate_id' => null,
        ]));

        return response()->json([
            'message' => 'Category created successfully.',
            'data' => new CategoryResource($category),
        ], 201);
    }

    public function show(ShowCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->where('stage_id', $validated['stage_id'])
            ->firstOrFail();

        return response()->json(new CategoryResource($category), 200);
    }

    public function update(UpdateCategoryRequest $request)
    {
        Log::debug('Raw Request', $request->all());
        
        try {
            $validated = $request->validated();
            Log::debug('Validated', $validated);

            $category = Category::where('category_id', $validated['category_id'])
                ->where('event_id', $validated['event_id'])
                ->firstOrFail();

            $category->update($validated);

            return response()->json([
                'message' => 'Category updated successfully.',
                'category' => new CategoryResource($category),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation Error', ['errors' => $e->errors()]);
            return response()->json(['message' => 'Validation failed.', 'errors' => $e->errors()], 422);
        }
    }

    public function destroy(DestroyCategoryRequest $request)
    {
        $validated = $request->validated();
        $category = Category::where('category_id', $validated['category_id'])
            ->where('event_id', $validated['event_id'])
            ->where('stage_id', $validated['stage_id'])
            ->firstOrFail();

        $category->delete();

        return response()->json(['message' => 'Category deleted successfully.'], 204);
    }

    public function start(Request $request, $event_id, $category_id)
    {
        Log::info("CategoryController::start called", [
            'event_id' => $event_id,
            'category_id' => $category_id,
        ]);

        $category = Category::where('event_id', $event_id)->findOrFail($category_id);
        if ($category->status !== 'pending') {
            Log::warning("Category is not pending", ['category_status' => $category->status]);
            return response()->json(['message' => 'Category is not pending'], 400);
        }

        $stage = Stage::where('stage_id', $category->stage_id)->firstOrFail();
        if ($stage->status !== 'active') {
            Log::warning("Parent stage is not active", ['stage_status' => $stage->status]);
            return response()->json(['message' => 'Parent stage is not active'], 400);
        }

        // Check if any other category in the same stage is active
        $activeCategory = Category::where('stage_id', $category->stage_id)
            ->where('status', 'active')
            ->where('category_id', '!=', $category_id)
            ->exists();
        if ($activeCategory) {
            Log::warning("Another category in the stage is active", ['stage_id' => $category->stage_id]);
            return response()->json(['message' => 'Another category in this stage is already active'], 400);
        }

        try {
            $updated = $category->update(['status' => 'active']);
            $category->refresh();
            Log::info("After candidate set, current candidate is now:", [
                'category_id' => $category->category_id,
                'current_candidate_id' => $category->current_candidate_id,
            ]);

            if (!$updated || $category->status !== 'active') {
                Log::error("Failed to update category status to active", ['category_id' => $category_id]);
                return response()->json(['message' => 'Failed to update category status'], 500);
            }

            broadcast(new CategoryStatusUpdated($category_id, 'active', $event_id))->toOthers();
            return response()->json(['message' => 'Category started successfully']);
        } catch (\Exception $e) {
            Log::error("Exception in CategoryController::start", [
                'category_id' => $category_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function reset(Request $request, $event_id, $category_id)
    {
        Log::info("CategoryController::reset called", [
            'event_id' => $event_id,
            'category_id' => $category_id,
        ]);

        $category = Category::where('event_id', $event_id)->findOrFail($category_id);

        if (!in_array($category->status, ['active', 'finalized'])) {
            Log::warning("Category is neither active nor finalized", ['category_status' => $category->status]);
            return response()->json(['message' => 'Only active or finalized categories can be reset'], 400);
        }

        try {
            // Clear associated scores
            $deletedScores = Score::where('category_id', $category_id)->delete();
            Log::info("Scores deleted for category", [
                'category_id' => $category_id,
                'deleted_count' => $deletedScores,
            ]);

            // Reset the category's status and current candidate
            $updated = $category->update([
                'status' => 'pending',
                'current_candidate_id' => null,
            ]);
            $category->refresh();
            Log::info("Category reset attempted", [
                'category_id' => $category_id,
                'updated' => $updated,
                'new_status' => $category->status,
                'current_candidate_id' => $category->current_candidate_id,
            ]);

            if (!$updated || $category->status !== 'pending') {
                Log::error("Failed to reset category status to pending", ['category_id' => $category_id]);
                return response()->json(['message' => 'Failed to reset category status'], 500);
            }

            // Trigger real-time event update
            broadcast(new CategoryStatusUpdated($category_id, 'pending', $event_id))->toOthers();
            return response()->json(['message' => 'Category reset successfully']);
        } catch (\Exception $e) {
            Log::error("Exception in CategoryController::reset", [
                'category_id' => $category_id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Server error: ' . $e->getMessage()], 500);
        }
    }

    public function setCandidate(Request $request, $event_id, $category_id)
    {
        $request->validate([
            'candidate_id' => 'required|exists:candidates,candidate_id',
        ]);

        Log::info("CategoryController::setCandidate called", [
            'event_id' => $event_id,
            'category_id' => $category_id,
            'request_data' => $request->all(),
        ]);

        $category = Category::where('event_id', $event_id)->findOrFail($category_id);

        if ($category->status !== 'active') {
            Log::warning("Category is not active", ['category_status' => $category->status]);
            return response()->json(['message' => 'Category is not active'], 400);
        }

        $candidate_id = $request->input('candidate_id');
        $candidate = Candidate::where('event_id', $event_id)
            ->where('is_active', true)
            ->findOrFail($candidate_id);

        if ($category->current_candidate_id) {
            // Get all judges for the event
            $judges = Judge::where('event_id', $event_id)->pluck('judge_id');
            $judgeCount = $judges->count();

            // Count confirmed scores for the current candidate in this category
            $confirmedScores = Score::where('category_id', $category_id)
                ->where('candidate_id', $category->current_candidate_id)
                ->where('status', 'confirmed')
                ->whereIn('judge_id', $judges)
                ->count();

            // Check for temporary scores
            $pendingScores = Score::where('category_id', $category_id)
                ->where('candidate_id', $category->current_candidate_id)
                ->where('status', 'temporary')
                ->whereIn('judge_id', $judges)
                ->get();

            // Check all scores for debugging
            $allScores = Score::where('category_id', $category_id)
                ->where('candidate_id', $category->current_candidate_id)
                ->whereIn('judge_id', $judges)
                ->get();

            Log::info("Pending scores check", [
                'category_id' => $category_id,
                'current_candidate_id' => $category->current_candidate_id,
                'judge_count' => $judgeCount,
                'confirmed_scores_count' => $confirmedScores,
                'pending_scores_count' => $pendingScores->count(),
                'pending_scores' => $pendingScores->toArray(),
                'all_scores' => $allScores->toArray(),
            ]);

            // Block switching if not all judges have confirmed scores or if there are temporary scores
            if ($confirmedScores < $judgeCount || $pendingScores->isNotEmpty()) {
                Log::warning("Cannot switch candidate: not all judges have confirmed scores or pending scores exist", [
                    'category_id' => $category_id,
                    'current_candidate_id' => $category->current_candidate_id,
                    'attempted_new_candidate_id' => $candidate_id,
                    'confirmed_scores_count' => $confirmedScores,
                    'judge_count' => $judgeCount,
                ]);
                return response()->json(['message' => 'Cannot switch candidate until all judges have confirmed scores for the current candidate'], 400);
            }
        }

        $updated = $category->update(['current_candidate_id' => $candidate_id]);
        Log::info("Candidate set attempted", [
            'category_id' => $category_id,
            'candidate_id' => $candidate_id,
            'updated' => $updated,
        ]);

        if (!$updated) {
            Log::error("Failed to set candidate", ['category_id' => $category_id]);
            return response()->json(['message' => 'Failed to set candidate'], 500);
        }

        broadcast(new CandidateSet($event_id, $category_id, $candidate_id))->toOthers();
        return response()->json(['message' => 'Candidate set successfully']);
    }

    public function finalize(Request $request, $event_id, $category_id)
    {
        $category = Category::where('event_id', $event_id)->findOrFail($category_id);

        if ($category->status !== 'active') {
            return response()->json(['message' => 'Category is not active'], 400);
        }

        $candidates = Candidate::where('event_id', $event_id)->where('is_active', true)->pluck('candidate_id');
        $scoredCandidates = Score::where('category_id', $category_id)
            ->where('status', 'confirmed')
            ->distinct('candidate_id')
            ->pluck('candidate_id');

        if ($candidates->diff($scoredCandidates)->isNotEmpty()) {
            return response()->json(['message' => 'Not all candidates have been scored'], 400);
        }

        $category->update(['status' => 'finalized', 'current_candidate_id' => null]);
        broadcast(new CategoryStatusUpdated($category_id, 'finalized', $event_id))->toOthers();

        return response()->json(['message' => 'Category finalized successfully']);
    }

    public function hasPendingScores($event_id, $category_id)
    {
        Log::info('hasPendingScores called', [
            'event_id' => $event_id,
            'category_id' => $category_id,
        ]);
        try {
            $category = Category::where('event_id', $event_id)->findOrFail($category_id);
            if (!$category->current_candidate_id) {
                Log::info('No current candidate for category', ['category_id' => $category_id]);
                return response()->json(['has_pending_scores' => false]);
            }
            $judges = Judge::where('event_id', $event_id)->pluck('judge_id');
            $judgeCount = $judges->count();

            $confirmedScores = Score::where('category_id', $category_id)
                ->where('candidate_id', $category->current_candidate_id)
                ->where('status', 'confirmed')
                ->whereIn('judge_id', $judges)
                ->count();

            $temporaryScores = Score::where('category_id', $category_id)
                ->where('candidate_id', $category->current_candidate_id)
                ->where('status', 'temporary')
                ->whereIn('judge_id', $judges)
                ->exists();

            $hasPendingScores = $confirmedScores < $judgeCount || $temporaryScores;

            Log::info('Pending scores check result', [
                'category_id' => $category_id,
                'candidate_id' => $category->current_candidate_id,
                'judge_count' => $judgeCount,
                'confirmed_scores_count' => $confirmedScores,
                'has_temporary_scores' => $temporaryScores,
                'has_pending_scores' => $hasPendingScores,
            ]);

            return response()->json(['has_pending_scores' => $hasPendingScores]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error in hasPendingScores', [
                'event_id' => $event_id,
                'category_id' => $category_id,
                'errors' => $e->errors(),
            ]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in hasPendingScores', [
                'event_id' => $event_id,
                'category_id' => $category_id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to check pending scores'], 500);
        }
    }

    public function hasPendingScoresForAll(Request $request, $event_id)
    {
        Log::info("hasPendingScoresForAll called", [
            'event_id' => $event_id,
            'request_params' => $request->all(),
        ]);

        $categories = Category::where('event_id', $event_id)->get();
        $judges = Judge::where('event_id', $event_id)->pluck('judge_id');
        $judgeCount = $judges->count();

        $results = [];
        foreach ($categories as $category) {
            $hasPendingScores = false;
            // Debug: Log all scores for the category
            $allScores = Score::where('category_id', $category->category_id)
                ->whereIn('judge_id', $judges)
                ->get();
            Log::info("All scores for category", [
                'category_id' => $category->category_id,
                'scores' => $allScores->toArray(),
            ]);

            if ($category->current_candidate_id) {
                $confirmedScores = Score::where('category_id', $category->category_id)
                    ->where('candidate_id', $category->current_candidate_id)
                    ->where('status', 'confirmed')
                    ->whereIn('judge_id', $judges)
                    ->count();

                $temporaryScores = Score::where('category_id', $category->category_id)
                    ->where('candidate_id', $category->current_candidate_id)
                    ->where('status', 'temporary')
                    ->whereIn('judge_id', $judges)
                    ->exists();

                $hasPendingScores = $confirmedScores < $judgeCount || $temporaryScores;
            } else {
                // Check if any scores exist for the category
                $hasPendingScores = Score::where('category_id', $category->category_id)
                    ->whereIn('judge_id', $judges)
                    ->whereIn('status', ['temporary', 'confirmed'])
                    ->exists();
            }

            Log::info("Pending scores check for category", [
                'category_id' => $category->category_id,
                'current_candidate_id' => $category->current_candidate_id,
                'judge_count' => $judgeCount,
                'confirmed_scores_count' => $confirmedScores ?? 0,
                'has_temporary_scores' => $temporaryScores ?? false,
                'has_pending_scores' => $hasPendingScores,
            ]);

            $results[$category->category_id] = $hasPendingScores;
        }

        Log::info("Pending scores for all categories", [
            'event_id' => $event_id,
            'results' => $results,
        ]);

        return response()->json(['pending_scores' => $results]);
    }
}