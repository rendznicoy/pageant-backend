<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\Stage;
use App\Models\Category;
use App\Http\Requests\EventRequest\StoreEventRequest;
use App\Http\Requests\EventRequest\UpdateEventRequest;
use App\Http\Requests\EventRequest\DestroyEventRequest;
use App\Http\Requests\EventRequest\StartEventRequest;
use App\Http\Requests\EventRequest\FinalizeEventRequest;
use App\Http\Requests\EventRequest\ResetEventRequest;
use App\Http\Requests\EventRequest\ShowEventRequest;
use App\Http\Resources\EventResource;
use App\Jobs\DeleteJudgeAccounts;
use App\Events\EventStatusUpdated;
use App\Events\EventFinalized;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Score;
use App\Models\Candidate;

class EventController extends Controller
{
    public function index()
    {
        try {
            $events = Event::with('createdBy')
                ->withCount(['candidates', 'judges', 'categories'])
                ->get();
            return response()->json(EventResource::collection($events));
        } catch (\Exception $e) {
            Log::error('Failed to fetch events: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to fetch events'], 500);
        }
    }

    public function store(StoreEventRequest $request)
    {
        try {
            $validated = $request->validated();

            if ($request->hasFile('cover_photo')) {
                $file = $request->file('cover_photo');
                if ($file->isValid()) {
                    $path = $file->store('event_covers', 'public');
                    $validated['cover_photo'] = $path;
                } else {
                    throw new \Exception('Invalid file upload');
                }
            }

            $event = Event::create(array_merge($validated, ['status' => 'inactive']));

            Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => 'Default Stage',
                'status' => 'pending',
            ]);

            Category::create([
                'event_id' => $event->event_id,
                'stage_id' => $event->stages()->first()->stage_id,
                'category_name' => 'Default Category',
                'status' => 'pending',
                'current_candidate_id' => null,
                'category_weight' => 100,
                'max_score' => 10,
            ]);

            return response()->json([
                'message' => 'Event created successfully with default stage and category.',
                'event' => new EventResource($event)
            ], 201);
            Log::debug('Failed validation', [
                'statisticians_raw' => $this->input('statisticians'),
                'parsed' => json_decode($this->input('statisticians'), true),
                'all_input' => $this->all(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create event: ' . $e->getMessage(), [
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to create event'], 500);
        }
    }

    public function show(ShowEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::where('event_id', $validated['event_id'])->firstOrFail();
            
            $event->save();

            $event->load('createdBy')->loadCount(['candidates', 'judges', 'categories']);

            return response()->json(new EventResource($event), 200);
        } catch (\Exception $e) {
            Log::error('Show event error: ' . $e->getMessage(), [
                'event_id' => $validated['event_id'],
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to fetch event'], 500);
        }
    }

    public function update(UpdateEventRequest $request, $event_id)
    {
        try {
            $validated = $request->validated();
            $event = Event::findOrFail($event_id);

            $updateData = [];
            $fields = ['event_name', 'venue', 'description', 'start_date', 'end_date', 'division'];

            foreach ($fields as $field) {
                if (in_array($field, ['start_date', 'end_date']) && isset($validated[$field])) {
                    $submitted = Carbon::parse($validated[$field])->utc()->format('Y-m-d H:i:s');
                    $existing = $event->$field
                        ? Carbon::parse($event->$field)->utc()->format('Y-m-d H:i:s')
                        : null;

                    if ($submitted !== $existing) {
                        $updateData[$field] = $submitted;
                    }
                } elseif (isset($validated[$field])) {
                    $new = $validated[$field];
                    $old = $event->$field;
                    if ($new !== $old) {
                        $updateData[$field] = $new;
                    }
                }
            }

            // Compare cover photo
            if ($request->hasFile('cover_photo')) {
                $file = $request->file('cover_photo');
                if ($file->isValid()) {
                    $newName = $file->getClientOriginalName();
                    $oldName = $event->cover_photo ? basename($event->cover_photo) : '';
                    if ($newName !== $oldName) {
                        if ($event->cover_photo) {
                            Storage::disk('public')->delete($event->cover_photo);
                        }
                        $path = $file->store('event_covers', 'public');
                        $updateData['cover_photo'] = $path;
                    }
                }
            }

            // Handle statisticians change
            $incomingStats = $request->input('statisticians');
            $hasChangedStatisticians = $incomingStats !== $event->statisticians;

            // Save changes
            if (empty($updateData) && !$hasChangedStatisticians) {
                return response()->json([
                    'success' => false,
                    'message' => 'No changes were made to the event.',
                ], 200);
            }

            if (!empty($updateData)) {
                $event->update($updateData);
            }

            if ($hasChangedStatisticians) {
                $event->statisticians = $incomingStats;
                $event->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'Event updated successfully.',
                'event' => new EventResource($event->fresh()),
            ]);
        } catch (\Exception $e) {
            Log::error('Event update failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update event: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(DestroyEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::where('event_id', $validated['event_id'])->firstOrFail();
            $validated['statisticians'] = json_decode($request->input('statisticians'), true);
            if ($event->cover_photo) {
                Storage::disk('public')->delete($event->cover_photo);
            }
            $event->delete();

            return response()->json(['message' => 'Event deleted successfully.'], 204);
        } catch (\Exception $e) {
            Log::error('Delete event error: ' . $e->getMessage(), [
                'event_id' => $validated['event_id'],
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to delete event'], 500);
        }
    }

    public function start(StartEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

            if ($event->status !== 'inactive') {
                return response()->json(['message' => 'Only inactive events can be started.'], 400);
            }

            $event->update(['status' => 'active']);
            broadcast(new EventStatusUpdated($event->event_id, 'active'))->toOthers();

            $event->load('createdBy')->loadCount(['candidates', 'judges', 'categories']);

            return response()->json(['message' => 'Event started successfully.', 'event' => new EventResource($event)], 200);
        } catch (\Exception $e) {
            Log::error('Start event error: ' . $e->getMessage(), [
                'event_id' => $validated['event_id'],
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to start event'], 500);
        }
    }

    public function finalize(FinalizeEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

            if ($event->status !== 'active') {
                return response()->json(['message' => 'Only active events can be finalized.'], 400);
            }

            $activeStages = $event->stages()->where('status', '!=', 'finalized')->exists();
            if ($activeStages) {
                return response()->json(['message' => 'Not all stages are finalized'], 400);
            }

            $event->update(['status' => 'completed']);
            broadcast(new EventStatusUpdated($event->event_id, 'completed'))->toOthers();
            try {
                broadcast(new EventFinalized($event->event_id))->toOthers();
                Log::info('EventFinalized broadcast attempted', ['event_id' => $event->event_id]);
            } catch (\Exception $e) {
                Log::error('Failed to broadcast EventFinalized', [
                    'event_id' => $event->event_id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
            DeleteJudgeAccounts::dispatch($event->event_id)->delay(now()->addMinute());

            $event->load('createdBy')->loadCount(['candidates', 'judges', 'categories']);

            return response()->json(['message' => 'Event finalized successfully.', 'event' => new EventResource($event)], 200);
        } catch (\Exception $e) {
            Log::error('Finalize event error: ' . $e->getMessage(), [
                'event_id' => $validated['event_id'],
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to finalize event'], 500);
        }
    }

    public function reset(ResetEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

            if (!in_array($event->status, ['active', 'completed'])) {
                return response()->json(['message' => 'Only active or completed events can be reset.'], 400);
            }

            $deletedScores = Score::where('event_id', $validated['event_id'])->delete();
            Log::info("Scores deleted for event", [
                'event_id' => $validated['event_id'],
                'deleted_count' => $deletedScores,
            ]);

            $updatedStages = Stage::where('event_id', $validated['event_id'])->update([
                'status' => 'pending',
                'top_candidates_count' => null,
            ]);
            Log::info("Stages reset for event", [
                'event_id' => $validated['event_id'],
                'updated_count' => $updatedStages,
            ]);

            $updatedCategories = Category::where('event_id', $validated['event_id'])->update([
                'status' => 'pending',
                'current_candidate_id' => null,
            ]);
            Log::info("Categories reset for event", [
                'event_id' => $validated['event_id'],
                'updated_count' => $updatedCategories,
            ]);

            $event->update(['status' => 'inactive']);
            broadcast(new EventStatusUpdated($event->event_id, 'inactive'))->toOthers();

            $event->load('createdBy')->loadCount(['candidates', 'judges', 'categories']);

            return response()->json(['message' => 'Event reset successfully.', 'event' => new EventResource($event)], 200);
        } catch (\Exception $e) {
            Log::error('Reset event error: ' . $e->getMessage(), [
                'event_id' => $validated['event_id'],
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to reset event'], 500);
        }
    }

    public function toggleStar(Request $request, $event_id)
    {
        try {
            $event = Event::where('event_id', $event_id)->firstOrFail();
            $event->update([
                'is_starred' => !$event->is_starred
            ]);
            $event->load('createdBy')->loadCount(['candidates', 'judges', 'categories']);

            return response()->json([
                'message' => 'Event star status updated.',
                'event' => new EventResource($event),
            ]);
        } catch (\Exception $e) {
            Log::error('Toggle star error: ' . $e->getMessage(), [
                'event_id' => $event_id,
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json(['message' => 'Failed to toggle star'], 500);
        }
    }

    public function changeDivision(Request $request, $event_id)
    {
        $request->validate([
            'division' => 'required|in:standard,male-only,female-only',
        ]);

        $event = Event::findOrFail($event_id);

        if ($event->division === $request->division) {
            return response()->json([
                'message' => 'Division is already set to ' . $request->division,
            ], 200);
        }

        $event->update(['division' => $request->division]);

        // Reset candidates
        Score::where('event_id', $event_id)->delete();
        Candidate::where('event_id', $event_id)->delete();

        return response()->json([
            'message' => 'Division updated and candidates reset.',
            'event' => new EventResource($event),
        ], 200);
    }

    public function updateGlobalMaxScore(Request $request, $event_id)
    {
        $request->validate([
            'global_max_score' => 'required|integer|min:1|max:100',
        ]);

        $event = Event::findOrFail($event_id);
        $event->update(['global_max_score' => $request->global_max_score]);

        // Update all existing categories with the new max score
        Category::where('event_id', $event_id)
            ->update(['max_score' => $request->global_max_score]);

        return response()->json([
            'message' => 'Global max score updated successfully.',
            'global_max_score' => $request->global_max_score
        ]);
    }
}