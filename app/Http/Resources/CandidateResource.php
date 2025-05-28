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
            'photo' => $this->getCoverPhotoUrl(),
            'is_active' => $this->is_active, // Add is_active
        ];
    }

    private function getCoverPhotoUrl(): ?string
    {
        // Get raw database attributes to avoid model accessors
        $attributes = $this->resource->getAttributes();
        
        // Priority 1: Cloudinary URL
        if (!empty($attributes['photo_url'])) {
            return $attributes['photo_url'];
        }
        
        // Priority 2: Legacy cover_photo field (skip temp files)
        if (!empty($attributes['photo'])) {
            $photo = $attributes['photo'];
            
            // Skip temporary files
            if (str_contains($photo, '/tmp/')) {
                return null;
            }
            
            // If it's already a full URL, return as-is
            if (str_starts_with($photo, 'http')) {
                return $photo;
            }
            
            // Build local storage URL
            return asset('storage/' . ltrim($photo, '/'));
        }
        
        return null;
    }
}
