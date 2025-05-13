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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Score;

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
            
            $stage = Stage::create([
                'event_id' => $event->event_id,
                'stage_name' => 'Default Stage',
                'status' => 'pending',
            ]);
            
            Category::create([
                'event_id' => $event->event_id,
                'stage_id' => $stage->stage_id,
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
            
            $event->last_accessed = now();
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

    public function update(UpdateEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::findOrFail($validated['event_id']);

            Log::info('Update request data:', $validated);
            
            if (isset($validated['start_date'])) {
                Log::info('Original start_date: ' . $validated['start_date']);
                $validated['start_date'] = Carbon::parse($validated['start_date'])
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');
                Log::info('Formatted start_date for database: ' . $validated['start_date']);
            }
            
            if (isset($validated['end_date'])) {
                Log::info('Original end_date: ' . $validated['end_date']);
                $validated['end_date'] = Carbon::parse($validated['end_date'])
                    ->setTimezone('UTC')
                    ->format('Y-m-d H:i:s');
                Log::info('Formatted end_date for database: ' . $validated['end_date']);
            }

            if ($request->hasFile('cover_photo')) {
                $file = $request->file('cover_photo');
                if (!$file->isValid()) {
                    throw new \Exception('Invalid file upload');
                }
                
                if ($event->cover_photo) {
                    Storage::disk('public')->delete($event->cover_photo);
                }

                $path = $file->store('event_covers', 'public');
                $validated['cover_photo'] = $path;
            }

            $event->update($validated);
            
            return response()->json([
                'message' => 'Event updated successfully.',
                'event' => new EventResource($event)
            ]);
        } catch (\Exception $fileException) {
            Log::error('File upload error: ' . $fileException->getMessage());
            return response()->json(['message' => 'Failed to upload file: ' . $fileException->getMessage()], 500);
        } catch (\Exception $databaseException) {
            Log::error('Database update error: ' . $databaseException->getMessage());
            return response()->json(['message' => 'Failed to update event data: ' . $databaseException->getMessage()], 500);
        }  
    }

    public function destroy(DestroyEventRequest $request)
    {
        try {
            $validated = $request->validated();
            $event = Event::where('event_id', $validated['event_id'])->firstOrFail();
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
            DeleteJudgeAccounts::dispatch($event->event_id)->delay(now()->addMinutes(5));

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

            // Delete all scores for this event
            $deletedScores = Score::where('event_id', $validated['event_id'])->delete();
            Log::info("Scores deleted for event", [
                'event_id' => $validated['event_id'],
                'deleted_count' => $deletedScores,
            ]);

            // Reset all stages to pending
            $updatedStages = Stage::where('event_id', $validated['event_id'])->update([
                'status' => 'pending',
                'top_candidates_count' => null,
            ]);
            Log::info("Stages reset for event", [
                'event_id' => $validated['event_id'],
                'updated_count' => $updatedStages,
            ]);

            // Reset all categories to pending and clear current_candidate_id
            $updatedCategories = Category::where('event_id', $validated['event_id'])->update([
                'status' => 'pending',
                'current_candidate_id' => null,
            ]);
            Log::info("Categories reset for event", [
                'event_id' => $validated['event_id'],
                'updated_count' => $updatedCategories,
            ]);

            // Reset event status
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
}