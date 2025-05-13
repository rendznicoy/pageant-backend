<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class CategoryStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $event_id;
    public $category_id;
    public $status;

    public function __construct($event_id, $category_id, $status)
    {
        $this->event_id = $event_id;
        $this->category_id = $category_id;
        $this->status = $status;
    }

    public function broadcastOn()
    {
        return new Channel("event.{$this->event_id}");
    }
}