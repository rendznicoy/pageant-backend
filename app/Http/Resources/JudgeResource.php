<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JudgeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'judge_id' => $this->judge_id,
            'user' => new UserResource($this->whenLoaded('user')),
            'event_id' => $this->event_id,
        ];
    }
}
