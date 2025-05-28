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
use App\Services\CloudinaryService;

class CandidateController extends Controller
{
    protected $cloudinaryService;

    public function __construct(CloudinaryService $cloudinaryService)
    {
        $this->cloudinaryService = $cloudinaryService;
    }

    public function index(Request $request, $event_id) {
        $query = Candidate::where('event_id', $event_id);
        if ($request->has('sex')) {
            $query->where('sex', $request->query('sex'));
        }
    
        $candidates = $query->get();
    
        // 👇 Add this to debug the raw DB values
        Log::info('Fetched candidates raw from DB', $candidates->toArray());
    
        return response()->json(['data' => CandidateResource::collection($candidates)]);
    }

    public function store(StoreCandidateRequest $request)
    {
        $validated = $request->validated();

        $event = Event::findOrFail($request->event_id);

        if ($event->division === 'male-only') {
            $validated['sex'] = 'M';
        } elseif ($event->division === 'female-only') {
            $validated['sex'] = 'F';
        }

        $exists = Candidate::where('event_id', $event->event_id)
            ->where('candidate_number', $request->candidate_number)
            ->exists();

        if (($event->division !== 'standard') && $exists) {
            return response()->json(['message' => 'Candidate number must be unique.'], 422);
        }

        // Handle Cloudinary photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if ($file->isValid()) {
                $uploadResult = $this->cloudinaryService->upload($file, 'candidate_photos');
                if ($uploadResult) {
                    $validated['photo_url'] = $uploadResult['url'];
                    $validated['photo_public_id'] = $uploadResult['public_id'];
                }
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
        $candidate = Candidate::where('candidate_id', $request->candidate_id)
            ->where('event_id', $request->event_id)
            ->firstOrFail();

        $updated = false;

        // Update regular fields
        $fields = ['first_name', 'last_name', 'candidate_number', 'sex', 'team'];
        foreach ($fields as $field) {
            $newValue = $request->input($field);
            if ($newValue !== null && $candidate->$field !== $newValue) {
                $candidate->$field = $newValue;
                $updated = true;
            }
        }

        // Handle is_active as boolean
        if ($request->has('is_active')) {
            $newIsActive = filter_var($request->input('is_active'), FILTER_VALIDATE_BOOLEAN);
            if ($candidate->is_active !== $newIsActive) {
                $candidate->is_active = $newIsActive;
                $updated = true;
            }
        }

        // Handle Cloudinary photo upload
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            if ($file->isValid()) {
                // Delete old image from Cloudinary
                if ($candidate->photo_public_id) {
                    $this->cloudinaryService->delete($candidate->photo_public_id);
                }

                // Upload new image
                $uploadResult = $this->cloudinaryService->upload($file, 'candidate_photos');
                if ($uploadResult) {
                    $candidate->photo_url = $uploadResult['url'];
                    $candidate->photo_public_id = $uploadResult['public_id'];
                    $updated = true;
                }
            }
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

        // Delete photo from Cloudinary
        if ($candidate->photo_public_id) {
            $this->cloudinaryService->delete($candidate->photo_public_id);
        }

        $candidate->delete();

        return response()->json(['message' => 'Candidate deleted successfully.'], 204);
    }

    public function resetCandidates($id)
    {
        $candidates = Candidate::where('event_id', $id)->get();
        
        // Delete all candidate photos from Cloudinary
        foreach ($candidates as $candidate) {
            if ($candidate->photo_public_id) {
                $this->cloudinaryService->delete($candidate->photo_public_id);
            }
        }

        Candidate::where('event_id', $id)->delete();
        
        return response()->json(['message' => 'Candidates reset successfully']);
    }
}
