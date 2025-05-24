<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StageResource extends JsonResource
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
            'stage_id' => $this->stage_id,
            'event_id' => $this->event_id,
            'name' => $this->stage_name,
            'status' => $this->status,
            'top_candidates_count' => $this->top_candidates_count,
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}