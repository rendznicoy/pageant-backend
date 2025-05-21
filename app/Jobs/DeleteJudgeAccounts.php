<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Judge;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class DeleteJudgeAccounts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $event_id;

    public function __construct($event_id)
    {
        $this->event_id = $event_id;
    }

    public function handle()
    {
        try {
            $judges = Judge::where('event_id', $this->event_id)->get();
            foreach ($judges as $judge) {
                $user = User::find($judge->user_id);
                if ($user) {
                    $user->delete();
                }
                $judge->delete();
            }
            Log::info('Judge accounts deleted for event', [
                'event_id' => $this->event_id,
                'count' => $judges->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to delete judge accounts: ' . $e->getMessage(), [
                'event_id' => $this->event_id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}