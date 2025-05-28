<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
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
            'candidate_id' => $this->candidate_id,
            'event_id' => $this->event_id,
            'candidate_number' => $this->candidate_number,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'sex' => $this->sex,
            'team' => $this->team,
            'photo' => $this->photo_url, // Use Cloudinary URL
            'is_active' => $this->is_active, // Add is_active
        ];
    }
}
