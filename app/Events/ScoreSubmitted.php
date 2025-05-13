<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class ScoreSubmitted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $event_id;
    public $score_id;
    public $score;

    public function __construct($event_id, $score_id, $score)
    {
        $this->event_id = $event_id;
        $this->score_id = $score_id;
        $this->score = $score;
    }

    public function broadcastOn()
    {
        return new Channel("event.{$this->event_id}");
    }
}