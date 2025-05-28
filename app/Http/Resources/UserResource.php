<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public static $wrap = null;
    
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Enhanced photo URL logic with better fallbacks
        $profilePhoto = $this->getProfilePhotoUrl();

        return [
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'profile_photo' => $profilePhoto,
            'google_id' => $this->google_id,
            'email_verified_at' => $this->email_verified_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];    
    }

    /**
     * Get the appropriate profile photo URL with proper fallbacks
     */
    private function getProfilePhotoUrl(): string
    {
        // Priority 1: Cloudinary URL (best quality, optimized)
        if ($this->profile_photo_url) {
            return $this->profile_photo_url;
        }

        // Priority 2: Google profile photo (for OAuth users)
        if ($this->profile_photo && 
            filter_var($this->profile_photo, FILTER_VALIDATE_URL) && 
            strpos($this->profile_photo, 'google') !== false) {
            return $this->profile_photo;
        }

        // Priority 3: Local storage file
        if ($this->profile_photo && 
            !filter_var($this->profile_photo, FILTER_VALIDATE_URL)) {
            return asset('storage/' . $this->profile_photo);
        }

        // Priority 4: Default fallback
        return asset('uploads/profile_photos/default.png');
    }
}