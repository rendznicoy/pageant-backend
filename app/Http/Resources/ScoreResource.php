<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ScoreResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'event_id' => $this->event_id,
            'judge_id' => $this->judge_id,
            'candidate_id' => $this->candidate_id,
            'category_id' => $this->category_id,
            'score' => $this->score,
            'event' => new EventResource($this->whenLoaded('event')),
            'judge' => new JudgeResource($this->whenLoaded('judge')),
            'candidate' => new CandidateResource($this->whenLoaded('candidate')),
            'category' => new CategoryResource($this->whenLoaded('category')),
        ];
    }
}
