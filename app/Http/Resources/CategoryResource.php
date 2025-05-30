<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public static $wrap = false;

    public function toArray(Request $request): array
    {
        // Always use the event's global max score
        $event = $this->whenLoaded('event', function() {
            return $this->event;
        });
        
        // If event not loaded, load it
        if (!$event) {
            $event = \App\Models\Event::find($this->event_id);
        }
        
        $globalMaxScore = $event ? ($event->global_max_score ?? 100) : 100;

        return [
            'category_id' => $this->category_id,
            'event_id' => $this->event_id,
            'stage_id' => $this->stage_id,
            'name' => $this->category_name,
            'weight' => $this->category_weight,
            'max_score' => $globalMaxScore, // ✅ Always use global max score
            'status' => $this->status,
            'current_candidate_id' => $this->current_candidate_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}