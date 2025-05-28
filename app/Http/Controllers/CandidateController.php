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

    public function store(StoreCandidateRequest $request, $event_id)
    {
        try {
            $validated = $request->validated();
            $validated['event_id'] = $event_id; // Set from route parameter

            $event = Event::findOrFail($event_id);

            // Handle division-based sex assignment
            if ($event->division === 'male-only') {
                $validated['sex'] = 'M';
            } elseif ($event->division === 'female-only') {
                $validated['sex'] = 'F';
            }

            // Check for unique candidate number
            $exists = Candidate::where('event_id', $event_id)
                ->where('candidate_number', $validated['candidate_number'])
                ->exists();

            if ($exists) {
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

            // Set default is_active to true
            $validated['is_active'] = $validated['is_active'] ?? true;

            $candidate = Candidate::create($validated);

            return response()->json([
                'message' => 'Candidate created successfully.',
                'data' => new CandidateResource($candidate),
            ], 201);

        } catch (\Exception $e) {
            Log::error('Failed to create candidate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'message' => 'Failed to create candidate: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function show(ShowCandidateRequest $request) 
    {
        $validated = $request->validated();

        $candidate = Candidate::where('candidate_id', $validated['candidate_id'])
            ->where('event_id', $validated['event_id'])
            ->firstOrFail();

        return response()->json(new CandidateResource($candidate), 200);
    }

    public function update(UpdateCandidateRequest $request, $event_id, $candidate_id)
    {
        try {
            $validated = $request->validated();
            
            $candidate = Candidate::where('candidate_id', $candidate_id)
                ->where('event_id', $event_id)
                ->firstOrFail();

            $updated = false;

            // Update regular fields
            $fields = ['first_name', 'last_name', 'candidate_number', 'sex', 'team'];
            foreach ($fields as $field) {
                if (isset($validated[$field]) && $candidate->$field !== $validated[$field]) {
                    $candidate->$field = $validated[$field];
                    $updated = true;
                }
            }

            // Handle is_active as boolean
            if (isset($validated['is_active'])) {
                $newIsActive = $validated['is_active'];
                if ($candidate->is_active !== $newIsActive) {
                    $candidate->is_active = $newIsActive;
                    $updated = true;
                }
            }

            // Handle Cloudinary photo upload
            if ($request->hasFile('photo')) {
                $file = $request->file('photo');
                if ($file->isValid()) {
                    // Delete old image from Cloudinary if exists
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

        } catch (\Exception $e) {
            Log::error('Failed to update candidate', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return response()->json([
                'message' => 'Failed to update candidate: ' . $e->getMessage(),
            ], 500);
        }
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
