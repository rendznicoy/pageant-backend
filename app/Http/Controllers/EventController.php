<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Requests\EventRequest\StoreEventRequest;
use App\Http\Requests\EventRequest\UpdateEventRequest;
use App\Http\Requests\EventRequest\DestroyEventRequest;
use App\Http\Requests\EventRequest\StartEventRequest;
use App\Http\Requests\EventRequest\FinalizeEventRequest;
use App\Http\Requests\EventRequest\ResetEventRequest;
use App\Http\Requests\EventRequest\ShowEventRequest;
use App\Http\Resources\EventResource;
use App\Models\Candidate;
use App\Models\Score;

class EventController extends Controller
{
    public function index()
    {
        $events = Event::with('createdBy')->get();

        return response()->json(EventResource::collection($events));
    }

    public function store(StoreEventRequest $request)
    {
        $validated = $request->validated();
        $event = Event::create($validated);

        return response()->json([
            'message' => 'Event created successfully.',
            'data' => new EventResource($event),
        ], 201);
    }

    public function show(ShowEventRequest $request) 
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        return response()->json(new EventResource($event), 200);
    }

    public function update(UpdateEventRequest $request)
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();
        $event->update($validated);

        return response()->json([
            'message' => 'Event updated successfully.',
            'event' => new EventResource($event),
        ]);
    }

    public function destroy(DestroyEventRequest $request, $id) 
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return response()->json(['message' => 'Event deleted successfully.'], 204);
    }

    public function start(StartEventRequest $request)
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        if ($event->status !== 'inactive') {
            return response()->json(['message' => 'Only inactive events can be started.'], 400);
        }

        $event->status = 'active';
        $event->save();

        return response()->json(['message' => 'Event started successfully.', 'event' => new EventResource($event)], 200);
    }

    public function finalize(FinalizeEventRequest $request)
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        if ($event->status !== 'active') {
            return response()->json(['message' => 'Only active events can be finalized.'], 400);
        }

        $event->status = 'completed';
        $event->save();

        return response()->json(['message' => 'Event finalized successfully.', 'event' => new EventResource($event)], 200);
    }

    public function reset(ResetEventRequest $request)
    {
        $validated = $request->validated();

        $event = Event::where('event_id', $validated['event_id'])->firstOrFail();

        // Only inactive events can be safely reset in this setup
        if ($event->status !== 'inactive') {
            return response()->json(['message' => 'Only inactive events can be reset.'], 400);
        }

        // Optionally delete associated data (scores, candidates, etc.) here

        $event->update(['status' => 'inactive']);

        return response()->json(['message' => 'Event reset successfully.', 'event' => new EventResource($event)], 200);
    }
}
