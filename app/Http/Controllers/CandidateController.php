<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CandidateRequest\StoreCandidateRequest;
use App\Http\Requests\CandidateRequest\DestroyCandidateRequest;
use App\Http\Requests\CandidateRequest\ShowCandidateRequest;
use App\Http\Requests\CandidateRequest\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;

class CandidateController extends Controller
{
    public function index(Request $request)
    {
        $perPage = 12;

        $eventId = $request->query('event_id');
        $sex = $request->query('sex');
        $search = $request->query('search');

        $query = Candidate::query();

        if ($eventId) {
            $query->where('event_id', $eventId);
        }

        if ($sex && $sex !== 'all') {
            $query->where('sex', $sex);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%$search%")
                ->orWhere('last_name', 'like', "%$search%")
                ->orWhere('team', 'like', "%$search%");
            });
        }

        $candidates = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'data' => $candidates->items(),
            'meta' => [
                'current_page' => $candidates->currentPage(),
                'per_page' => $candidates->perPage(),
                'total' => $candidates->total(),
                'last_page' => $candidates->lastPage(),
            ]
        ], 200);
    }

    public function store(StoreCandidateRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('photo')) {
            $validated['photo'] = file_get_contents($request->file('photo')->getRealPath());
        }

        $candidate = Candidate::create($validated);

        return response()->json([
            'message' => 'Candidate created successfully.',
            'data' => new CandidateResource($candidate),
        ], 201);
    }

    public function show(ShowCandidateRequest $request) 
    {
        $validated = $request->validated();

        $candidate = Candidate::where('candidate_id', $validated['candidate_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        return response()->json(new CandidateResource($candidate), 200);
    }

    public function update(UpdateCandidateRequest $request) 
    {
        $validated = $request->validated();

        $candidate = Candidate::where('candidate_id', $validated['candidate_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        if ($request->hasFile('photo')) {
            $validated['photo'] = file_get_contents($request->file('photo')->getRealPath());
        }

        $candidate->update($validated);

        return response()->json([
            'message' => 'Candidate updated successfully.',
            'candidate' => new CandidateResource($candidate),
        ]);
    }

    public function destroy(DestroyCandidateRequest $request) 
    {
        $validated = $request->validated();

        $candidate = Candidate::where('candidate_id', $validated['candidate_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        $candidate->delete();

        return response()->json(['message' => 'Candidate deleted successfully.'], 204);
    }
}
