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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CandidateController extends Controller
{
    public function index(Request $request, $event_id) {
        $query = Candidate::where('event_id', $event_id); // ✅ fetch all regardless of status
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
        Log::info('Raw update request payload', $request->all());

        $candidate = Candidate::where('candidate_id', $request->candidate_id)
            ->where('event_id', $request->event_id)
            ->firstOrFail();

        $updated = false;

        // Safely update fields only if changed
        $fields = ['first_name', 'last_name', 'candidate_number', 'sex', 'team'];
        foreach ($fields as $field) {
            $newValue = $request->input($field);
            if ($newValue !== null && $candidate->$field !== $newValue) {
                $candidate->$field = $newValue;
                $updated = true;
            }
            Log::info("Updating field $field: '{$candidate->$field}' → '$newValue'");
        }

        // Handle is_active as boolean (safely compare)
        if ($request->has('is_active')) {
            $newIsActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
            if ($candidate->is_active !== $newIsActive) {
                $candidate->is_active = $newIsActive;
                $updated = true;
            }
        }

        // Handle photo upload
        if ($request->hasFile('photo') && $request->file('photo')->isValid()) {
            if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
                Storage::disk('public')->delete($candidate->photo);
            }

            $path = $request->file('photo')->store('candidate_photos', 'public');
            $candidate->photo = $path;
            $updated = true;
        }

        if (!$updated) {
            return response()->json([
                'message' => 'No changes were made to the candidate.',
                'candidate' => new CandidateResource($candidate),
            ], 200);
        }

        $candidate->save();

        return response()->json([
            'message' => 'Candidate updated successfully.',
            'candidate' => new CandidateResource($candidate->fresh()),
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
