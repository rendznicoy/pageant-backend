<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Http\Requests\EventRequest\StoreEventRequest;
use App\Http\Resources\EventResource;

class EventController extends Controller
{
    public function index()
    {
        return EventResource::collection(Event::with('creator')->get());
    }

    public function store(StoreEventRequest $request)
    {
        $event = Event::create($request->validated());
        return new EventResource($event->load('creator'));
    }
}
