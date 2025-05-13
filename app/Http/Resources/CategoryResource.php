<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public static $wrap = false;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->category_id,
            'event_id' => $this->event_id,
            'stage_id' => $this->stage_id,
            'name' => $this->category_name,
            'weight' => $this->category_weight,
            'max_score' => $this->max_score,
            'status' => $this->status,
            'current_candidate_id' => $this->current_candidate_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
