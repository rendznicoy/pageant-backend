<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class StageStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $stage_id;
    public $status;
    public $event_id;

    public function __construct($stage_id, $status, $event_id)
    {
        $this->stage_id = $stage_id;
        $this->status = $status;
        $this->event_id = $event_id;
    }

    public function broadcastOn()
    {
        return new Channel("event.{$this->event_id}");
    }
}