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
use App\Models\Score;
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
        $user = auth()->user();
        if (!$user) {
            Log::error("No authenticated user found in currentSession", [
                'path' => $request->path(),
                'headers' => $request->headers->all(),
                'token' => $request->bearerToken(),
            ]);
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $judgeEntry = Judge::where('user_id', $user->user_id)->first();
        if (!$judgeEntry) {
            Log::warning("No judge entry found for user", [
                'user_id' => $user->user_id,
                'email' => $user->email,
            ]);
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        Log::info("JudgeController::currentSession called", [
            'judge_id' => $judgeEntry->judge_id,
            'user_id' => $user->user_id,
            'judge_role' => $user->role,
            'judge_email' => $user->email,
            'judge_username' => $user->username,
            'auth_check' => auth()->check(),
            'token' => Str::limit($request->bearerToken(), 20),
        ]);

        $event = Event::whereHas('judges', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })->first();
        if (!$event) {
            Log::warning("No event assigned to judge", [
                'judge_id' => $judgeEntry->judge_id,
                'user_id' => $user->user_id,
                'judge_email' => $user->email,
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

        $scoreStatus = 'none';
        if ($nextCandidate && $currentCategory) {
            $score = Score::findByCompositeKey([
                'judge_id' => $judgeEntry->judge_id,
                'candidate_id' => $nextCandidate->candidate_id,
                'category_id' => $currentCategory->category_id,
                'event_id' => $event->event_id,
            ]);
            $scoreStatus = $score ? $score->status : 'none';
            Log::info("Score status for judge", [
                'judge_id' => $judgeEntry->judge_id,
                'candidate_id' => $nextCandidate->candidate_id,
                'category_id' => $currentCategory->category_id,
                'event_id' => $event->event_id,
                'score_status' => $scoreStatus,
            ]);
        }

        return response()->json([
            'event' => [
                'event_id' => $event->event_id,
                'event_name' => $event->event_name,
                'venue' => $event->venue, // Added
                'status' => $event->status,
            ],
            'judge_name' => $user->first_name . ' ' . $user->last_name,
            'judge' => [
                'judge_id' => $judgeEntry->judge_id,
                'user_id' => $judgeEntry->user_id,
                'event_id' => $judgeEntry->event_id,
                'pin_code' => $judgeEntry->pin_code,
                'created_at' => $judgeEntry->created_at,
                'updated_at' => $judgeEntry->updated_at,
            ],
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
            ]] : [],
            'score_status' => $scoreStatus,
        ]);
    }
}