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
use Illuminate\Support\Facades\Storage;
use App\Services\CloudinaryService;

class JudgeController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index($event_id)
    {
        $judges = Judge::with('user')->where('event_id', $event_id)->get();
        return response()->json(['data' => JudgeResource::collection($judges)]);
    }

    public function store(StoreJudgeRequest $request, $event_id)
    {
        try {
            $data = $request->validated();
            $data['event_id'] = $event_id; // Set from route parameter

            $event = Event::find($event_id);
            if (!$event) {
                return response()->json(['message' => 'Event not found.'], 404);
            }

            $username = strtolower(explode('@', $request->email)[0]);
            $originalUsername = $username;
            $counter = 1;
            while (User::where('username', $username)->exists()) {
                $username = $originalUsername . $counter++;
            }

            // Handle Cloudinary photo upload
            $photoUrl = null;
            $photoPublicId = null;
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                $uploadResult = $this->cloudinaryService->upload($request->file('photo'), 'judge_photos');
                if ($uploadResult) {
                    $photoUrl = $uploadResult['url'];
                    $photoPublicId = $uploadResult['public_id'];
                }
            }

            $user = User::create([
                'username' => $username,
                'email' => $request->email,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'role' => 'judge',
                'password' => null,
                'profile_photo_url' => $photoUrl,
                'profile_photo_public_id' => $photoPublicId,
            ]);

            do {
                $pin = strtoupper(Str::random(6));
            } while (Judge::where('pin_code', $pin)->exists());

            $judge = Judge::create([
                'event_id' => $event_id,
                'user_id' => $user->user_id,
                'pin_code' => $pin,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Judge created successfully.',
                'data' => new JudgeResource($judge),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create judge', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'message' => 'Failed to create judge: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(ShowJudgeRequest $request)
    {
        $validated = $request->validated();

        $judge = Judge::where('judge_id', $validated['judge_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        return response()->json(new JudgeResource($judge), 200);
    }

    public function update(UpdateJudgeRequest $request, $event_id, $judge_id)
    {
        try {
            Log::info('Judge update full request', $request->all());

            $validated = $request->validated();
            Log::info('Judge update validated', $validated);

            $judge = Judge::where('judge_id', $judge_id)
                ->where('event_id', $event_id)
                ->firstOrFail();

            // Update related user
            $user = $judge->user;
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Associated user not found.',
                ], 404);
            }

            $updated = false;

            // Update user fields
            if (isset($validated['first_name']) && $user->first_name !== $validated['first_name']) {
                $user->first_name = $validated['first_name'];
                $updated = true;
            }

            if (isset($validated['last_name']) && $user->last_name !== $validated['last_name']) {
                $user->last_name = $validated['last_name'];
                $updated = true;
            }

            if (isset($validated['email']) && $user->email !== $validated['email']) {
                $user->email = $validated['email'];
                $updated = true;
            }

            // Handle Cloudinary photo upload
            if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
                // Delete old image from Cloudinary if exists
                if ($user->profile_photo_public_id) {
                    $this->cloudinaryService->delete($user->profile_photo_public_id);
                }

                // Upload new image
                $uploadResult = $this->cloudinaryService->upload($request->file('photo'), 'judge_photos');
                if ($uploadResult) {
                    $user->profile_photo_url = $uploadResult['url'];
                    $user->profile_photo_public_id = $uploadResult['public_id'];
                    $updated = true;
                }
            }

            if ($updated) {
                $user->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Judge updated successfully.',
                'judge' => new JudgeResource($judge->fresh()),
            ]);

        } catch (\Exception $e) {
            Log::error('Judge update failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all(),
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update judge: ' . $e->getMessage(),
            ], 500);
        }
    }


    public function destroy(DestroyJudgeRequest $request, $event_id, $judge_id)
    {
        try {
            $judge = Judge::where('judge_id', $judge_id)
                ->where('event_id', $event_id)
                ->firstOrFail();

            $user = $judge->user;

            // Delete the judge first
            $judge->delete();

            // Delete image from Cloudinary and then the user
            if ($user) {
                if ($user->profile_photo_public_id) {
                    $this->cloudinaryService->delete($user->profile_photo_public_id);
                }
                $user->delete();
            }

            return response()->json(['message' => 'Judge and associated user deleted successfully.'], 204);

        } catch (\Exception $e) {
            Log::error('Failed to delete judge', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json(['message' => 'Failed to delete judge: ' . $e->getMessage()], 500);
        }
    }

    public function currentSession(Request $request)
    {
        $user = auth()->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $judgeEntry = Judge::where('user_id', $user->user_id)->first();
        if (!$judgeEntry) {
            return response()->json(['message' => 'No judge entry assigned'], 404);
        }

        $event = Event::whereHas('judges', function ($query) use ($user) {
            $query->where('user_id', $user->user_id);
        })->first();
        if (!$event) {
            return response()->json(['message' => 'No event assigned'], 404);
        }

        $currentCategory = Category::where('event_id', $event->event_id)
            ->where('status', 'active')
            ->with('stage')
            ->first();
            
        $nextCandidate = $currentCategory
            ? Candidate::where('event_id', $event->event_id)
                ->where('candidate_id', $currentCategory->current_candidate_id)
                ->first()
            : null;

        $scoreStatus = 'none';
        $scoreData = null;

        // Only check for scores if we have both a candidate and category
        if ($nextCandidate && $currentCategory) {
            $score = Score::findByCompositeKey([
                'judge_id' => $judgeEntry->judge_id,
                'candidate_id' => $nextCandidate->candidate_id,
                'category_id' => $currentCategory->category_id,
                'event_id' => $event->event_id,
            ]);
            
            // Extra protection - ensure score matches current candidate
            if ($score && $score->candidate_id !== $nextCandidate->candidate_id) {
                Log::warning("Mismatched candidate in score lookup", [
                    'expected_candidate_id' => $nextCandidate->candidate_id,
                    'actual_candidate_id' => $score->candidate_id,
                ]);
                $score = null;
            }
            
            if ($score) {
                $scoreStatus = $score->status; // 'temporary' or 'confirmed'
                $scoreData = [
                    'score' => $score->score,
                    'comments' => $score->comments,
                ];
            }
        }

        return response()->json([
            'event' => [
                'event_id' => $event->event_id,
                'event_name' => $event->event_name,
                'venue' => $event->venue,
                'status' => $event->status,
                'max_score' => $currentCategory ? $currentCategory->max_score : 100, // Add this
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
                'photo' => $nextCandidate->photo ? Storage::url($nextCandidate->photo) : null,
            ] : null,
            'score_status' => $scoreStatus,
            'score' => $scoreData['score'] ?? null,
            'comments' => $scoreData['comments'] ?? null,
        ]);
    }
}