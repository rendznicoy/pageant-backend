<?php

namespace App\Events;

use App\Models\Score;
use App\Models\Category;
use App\Models\Judge;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ScoreConfirmed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event_id;
    public $score_id;
    public $score;
    public $all_confirmed;

    public function __construct($event_id, $score_id, Score $score)
    {
        $this->event_id = $event_id;
        $this->score_id = $score_id;
        $this->score = $score;
        $category = Category::find($score->category_id);
        $judgeCount = Judge::where('event_id', $event_id)->count();
        $confirmedCount = Score::where('category_id', $score->category_id)
            ->where('candidate_id', $score->candidate_id)
            ->where('status', 'confirmed')
            ->count();
        $this->all_confirmed = $confirmedCount >= $judgeCount;

        Log::info("ScoreConfirmed event", [
            'event_id' => $event_id,
            'category_id' => $score->category_id,
            'candidate_id' => $score->candidate_id,
            'judge_count' => $judgeCount,
            'confirmed_count' => $confirmedCount,
            'all_confirmed' => $this->all_confirmed,
        ]);
    }

    public function broadcastOn()
    {
        return new Channel("event.{$this->event_id}");
    }

    public function broadcastWith()
    {
        return [
            'score' => [
                'score_id' => $this->score->score_id,
                'judge_id' => $this->score->judge_id,
                'candidate_id' => $this->score->candidate_id,
                'category_id' => $this->score->category_id,
                'score' => $this->score->score,
                'comments' => $this->score->comments,
            ],
            'all_confirmed' => $this->all_confirmed,
        ];
    }
}