<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Judge;
use Illuminate\Support\Facades\Log;

class ClearJudgePinCodes implements ShouldQueue
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
                // Clear the pin code but keep the judge and user records
                $judge->update(['pin_code' => null]);
                Log::info('Pin code cleared for judge', [
                    'judge_id' => $judge->judge_id,
                    'user_id' => $judge->user_id,
                    'event_id' => $this->event_id,
                ]);
            }

            Log::info('Judge pin codes cleared for event', [
                'event_id' => $this->event_id,
                'count' => $judges->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to clear judge pin codes: ' . $e->getMessage(), [
                'event_id' => $this->event_id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}