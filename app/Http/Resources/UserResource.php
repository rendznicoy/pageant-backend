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
        return [
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'profile_photo' => $this->profile_photo 
                ? secure_asset($this->profile_photo) // Use secure_asset instead of asset
                : secure_asset('uploads/profile_photos/default.png'),
        ];
    }
}
