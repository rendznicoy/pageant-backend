<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\JudgeRequest\StoreJudgeRequest;
use App\Http\Requests\JudgeRequest\DestroyJudgeRequest;
use App\Http\Requests\JudgeRequest\ShowJudgeRequest;
use App\Http\Requests\JudgeRequest\UpdateJudgeRequest;
use App\Http\Resources\JudgeResource;
use App\Models\Judge;
use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use App\Models\Candidate;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class JudgeController extends Controller
{
    public function index($event_id)
    {
        $judges = Judge::with('user')->where('event_id', $event_id)->get();
        return response()->json(['data' => JudgeResource::collection($judges)]);
    }

    public function store(StoreJudgeRequest $request)
    {
        $data = $request->validated();

        $event = Event::find($data['event_id']);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        $username = strtolower(explode('@', $request->email)[0]);
        $originalUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter++;
        }

        $user = User::create([
            'username' => $username,
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'judge',
            'password' => null,
        ]);

        do {
            $pin = strtoupper(Str::random(6));
        } while (Judge::where('pin_code', $pin)->exists());

        $judge = Judge::create([
            'event_id' => $request->event_id,
            'user_id' => $user->user_id,
            'pin_code' => $pin,
        ]);

        return response()->json([
            'success' => true,
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

    public function currentSession(Request $request)
    {
        $judge = auth()->user();
        if (!$judge) {
            Log::error("No authenticated user found in currentSession");
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        Log::info("JudgeController::currentSession called", [
            'judge_id' => $judge->user_id ?? 'null',
            'judge_role' => $judge->role ?? 'null',
            'judge_email' => $judge->email ?? 'null',
            'judge_username' => $judge->username ?? 'null',
            'auth_check' => auth()->check(),
            'user_attributes' => (array)$judge
        ]);
        if (!$judge->user_id) {
            Log::error("Authenticated user has no user_id", [
                'email' => $judge->email,
                'role' => $judge->role,
                'attributes' => (array)$judge
            ]);
            return response()->json(['message' => 'User ID not found'], 500);
        }
        $judgeEntry = Judge::where('user_id', $judge->user_id)->first();
        if (!$judgeEntry) {
            Log::warning("No judge entry found for user", [
                'user_id' => $judge->user_id,
                'email' => $judge->email,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }
        $event = Event::whereHas('judges', function ($query) use ($judge) {
            $query->where('user_id', $judge->user_id);
        })->first();
        if (!$event) {
            Log::warning("No event assigned to judge", [
                'user_id' => $judge->user_id,
                'judge_email' => $judge->email,
                'judge_username' => $judge->username,
            ]);
            return response()->json(['message' => 'No event assigned'], 404);
        }
        Log::info("Event found for judge", [
            'event_id' => $event->event_id,
            'event_name' => $event->event_name,
            'event_status' => $event->status,
        ]);
        $currentCategory = Category::where('event_id', $event->event_id)
            ->where('status', 'active')
            ->with('stage')
            ->first();
        $nextCandidate = $currentCategory
            ? Candidate::where('event_id', $event->event_id)
                ->where('is_active', true)
                ->where('candidate_id', $currentCategory->current_candidate_id)
                ->first()
            : null;

        return response()->json([
            'event' => [
                'event_id' => $event->event_id,
                'event_name' => $event->event_name,
                'status' => $event->status,
            ],
            'judge_name' => $judge->first_name . ' ' . $judge->last_name,
            'judge' => $judgeEntry,
            'current_category' => $currentCategory ? [
                'category_id' => $currentCategory->category_id,
                'category_name' => $currentCategory->category_name,
                'stage_id' => $currentCategory->stage_id,
                'stage_name' => $currentCategory->stage->stage_name,
                'max_score' => $currentCategory->max_score,
            ] : null,
            'next_candidate' => $nextCandidate ? [
                'candidate_id' => $nextCandidate->candidate_id,
                'candidate_number' => $nextCandidate->candidate_number,
                'first_name' => $nextCandidate->first_name,
                'last_name' => $nextCandidate->last_name,
                'team' => $nextCandidate->team,
                'photo' => $nextCandidate->photo,
            ] : null,
            'criteria' => $currentCategory ? [[
                'id' => $currentCategory->category_id,
                'name' => $currentCategory->category_name,
                'max_score' => $currentCategory->max_score,
            ]] : []
        ]);
    }

    public function scoringSession(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,event_id',
            'category_id' => 'required|exists:categories,category_id',
            'stage' => 'required|exists:stages,stage_id',
        ]);

        $judge = auth()->user();
        $category = Category::where('event_id', $request->event_id)
            ->where('category_id', $request->category_id)
            ->where('stage_id', $request->stage)
            ->where('status', 'active')
            ->firstOrFail();

        $event = Event::findOrFail($request->event_id);
        if ($event->status !== 'active') {
            return response()->json(['message' => 'Event is not active'], 403);
        }

        $nextCandidate = Candidate::where('event_id', $request->event_id)
            ->where('is_active', true)
            ->where('candidate_id', $category->current_candidate_id)
            ->first();

        return response()->json([
            'event' => [
                'event_id' => $event->event_id,
                'event_name' => $event->event_name,
            ],
            'category' => [
                'category_id' => $category->category_id,
                'category_name' => $category->category_name,
                'max_score' => $category->max_score,
                'stage' => [
                    'stage_id' => $category->stage->stage_id,
                    'stage_name' => $category->stage->stage_name,
                ],
            ],
            'criteria' => [[
                'id' => $category->category_id,
                'name' => $category->category_name,
                'max_score' => $category->max_score,
            ]],
            'next_candidate' => $nextCandidate ? [
                'candidate_id' => $nextCandidate->candidate_id,
                'candidate_number' => $nextCandidate->candidate_number,
                'first_name' => $nextCandidate->first_name,
                'last_name' => $nextCandidate->last_name,
                'team' => $nextCandidate->team,
                'photo' => $nextCandidate->photo,
            ] : null,
        ]);
    }
}