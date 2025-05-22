<?php

namespace App\Listeners;

use App\Events\ScoreConfirmed;
use App\Models\Category;
use App\Models\Candidate;
use App\Events\CandidateSet;
use Illuminate\Support\Facades\Log;

class AdvanceCandidate
{
    public function handle(ScoreConfirmed $event)
    {
        if ($event->all_confirmed) {
            Log::info("All scores confirmed, advancing candidate", [
                'event_id' => $event->event_id,
                'category_id' => $event->score->category_id,
                'candidate_id' => $event->score->candidate_id,
            ]);

            $category = Category::find($event->score->category_id);
            if (!$category) {
                Log::error("Category not found", ['category_id' => $event->score->category_id]);
                return;
            }

            // Find the next active candidate
            $nextCandidate = Candidate::where('event_id', $event->event_id)
                ->where('is_active', true)
                ->where('candidate_number', '>', $event->score->candidate->candidate_number)
                ->orderBy('candidate_number')
                ->first();

            if ($nextCandidate) {
                $category->update(['current_candidate_id' => $nextCandidate->candidate_id]);
                broadcast(new CandidateSet($event->event_id, $category->category_id, $nextCandidate->candidate_id));
                Log::info("CandidateSet broadcast", [
                    'event_id' => $event->event_id,
                    'category_id' => $category->category_id,
                    'candidate_id' => $nextCandidate->candidate_id,
                ]);
            } else {
                $category->update(['current_candidate_id' => null, 'status' => 'completed']);
                Log::info("No more candidates, category completed", [
                    'category_id' => $category->category_id,
                ]);
            }
        }
    }
}