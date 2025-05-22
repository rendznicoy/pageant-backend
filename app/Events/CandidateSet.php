<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class CandidateSet implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $event_id;
    public $category_id; // Add category_id
    public $candidate_id;

    public function __construct($event_id, $category_id, $candidate_id)
    {
        $this->event_id = $event_id;
        $this->category_id = $category_id;
        $this->candidate_id = $candidate_id;
    }

    public function broadcastOn()
    {
        return new Channel("event.{$this->event_id}");
    }

    public function broadcastWith()
    {
        return [
            'event_id' => $this->event_id,
            'category_id' => $this->category_id,
            'candidate_id' => $this->candidate_id,
        ];
    }
}