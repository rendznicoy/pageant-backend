<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
            'start_date' => $this->start_date instanceof Carbon
                ? $this->start_date->toDateTimeString() // Include time
                : Carbon::parse($this->start_date)->toDateTimeString(),
            'end_date' => $this->end_date instanceof Carbon
                ? $this->end_date->toDateTimeString() // Include time
                : Carbon::parse($this->end_date)->toDateTimeString(),
            'status' => $this->status,
            'cover_photo' => $this->cover_photo ? Storage::url('public/' . $this->cover_photo) : null,
            'description' => $this->description,
            'last_accessed' => $this->last_accessed instanceof Carbon
                ? $this->last_accessed->toIso8601String()
                : ($this->last_accessed ? Carbon::parse($this->last_accessed)->toIso8601String() : null),
            'is_starred' => $this->is_starred,
            'created_by' => $this->whenLoaded('createdBy', fn() => [
                'user_id' => $this->createdBy?->user_id,
                'first_name' => $this->createdBy?->first_name,
                'last_name' => $this->createdBy?->last_name,
            ]),
            'candidates_count' => $this->whenCounted('candidates', fn() => $this->candidates_count),
            'judges_count' => $this->whenCounted('judges', fn() => $this->judges_count),
            'categories_count' => $this->whenCounted('categories', fn() => $this->categories_count),
            'created_at' => $this->created_at instanceof Carbon
            ? $this->created_at->toIso8601String()
            : ($this->created_at ? Carbon::parse($this->created_at)->toIso8601String() : null),
            'updated_at' => $this->updated_at instanceof Carbon
            ? $this->updated_at->toIso8601String()
            : ($this->updated_at ? Carbon::parse($this->updated_at)->toIso8601String() : null),
        ];
    }
}