<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JudgeResource extends JsonResource
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
            'judge_id' => $this->judge_id,
            'event_id' => $this->event_id,
            'user_id' => $this->user->user_id,
            'pin_code' => $this->pin_code,
            'first_name' => $this->user->first_name,
            'last_name' => $this->user->last_name,
            'role' => $this->user->role,
            'profile_photo' => $this->user->profile_photo 
                ? asset('storage/' . $this->user->profile_photo) 
                : asset('uploads/profile_photos/default.png'),
        ];
    }
}
