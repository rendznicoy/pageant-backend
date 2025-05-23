<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CandidateRequest\StoreCandidateRequest;
use App\Http\Requests\CandidateRequest\DestroyCandidateRequest;
use App\Http\Requests\CandidateRequest\ShowCandidateRequest;
use App\Http\Requests\CandidateRequest\UpdateCandidateRequest;
use App\Http\Resources\CandidateResource;
use App\Models\Candidate;
use App\Models\Event;

class CandidateController extends Controller
{
    public function index(Request $request, $event_id) {
        $query = Candidate::where('event_id', $event_id)
            ->where('is_active', true); // Add is_active filter
        if ($request->has('sex')) {
            $query->where('sex', $request->query('sex'));
        }
        $candidates = $query->get();
        return response()->json(['data' => CandidateResource::collection($candidates)]);
    }

    public function store(StoreCandidateRequest $request)
    {
        $validated = $request->validated();

        $event = Event::findOrFail($request->event_id);

        if ($event->division === 'male-only') {
            $request->merge(['sex' => 'M']);
        } elseif ($event->division === 'female-only') {
            $request->merge(['sex' => 'F']);
        }

        $exists = Candidate::where('event_id', $event->id)
            ->where('candidate_number', $request->candidate_number)
            ->exists();

        if (($event->division !== 'standard') && $exists) {
            return response()->json(['message' => 'Candidate number must be unique.'], 422);
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if ($file->isValid()) {
                $validated['photo'] = $file->store('candidate_photos', 'public');
            }
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

    public function resetCandidates($id)
    {
        Candidate::where('event_id', $id)->delete();
        return response()->json(['message' => 'Candidates reset successfully']);
    }
}
