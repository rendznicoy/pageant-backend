<?php

namespace App\Jobs;

use App\Models\Judge;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteJudgeAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $eventId;

    /**
     * Create a new job instance.
     */
    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $judges = Judge::where('event_id', $this->eventId)->get();
        foreach ($judges as $judge) {
            User::where('id', $judge->user_id)->delete();
            $judge->delete();
        }
    }
}
