<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'event_name' => $this->event_name,
            'event_code' => $this->event_code,
            'event_date' => $this->event_date,
            'status' => $this->status,
            'created_by' => new UserResource($this->whenLoaded('creator')),
        ];
    }
}
