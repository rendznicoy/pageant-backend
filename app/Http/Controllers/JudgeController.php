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
use Illuminate\Support\Str;

class JudgeController extends Controller
{
    public function index($event_id)
    {
        $judges = Judge::with('user')->where('event_id', $event_id)->get();

        return response()->json(JudgeResource::collection($judges));
    }

    public function store(StoreJudgeRequest $request)
    {
        $data = $request->validated();

        $event = Event::find($data['event_id']);

        if (!$event) {
            return response()->json(['message' => 'Event not found.'], 404);
        }

        $username = strtolower(explode('@', $request->email)[0]); // basic username from email

        // Make sure the username is unique
        $originalUsername = $username;
        $counter = 1;
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter++;
        }

        // Create user first
        $user = User::create([
            'username' => $request->email, // or generate a username
            'email' => $request->email,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'role' => 'judge',
            'password' => null, // assuming login via pin_code
        ]);

        // Generate unique pin_code
        do {
            $pin = strtoupper(Str::random(6));
        } while (Judge::where('pin_code', $pin)->exists());

        // Create judge
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
}
