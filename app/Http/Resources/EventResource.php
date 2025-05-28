<?php
// app/Http/Resources/EventResource.php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Judge;
use App\Models\Score;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $judges = Judge::where('event_id', $this->event_id)->with('user')->get();

        $pendingJudges = [];
        foreach ($this->categories as $category) {
            if (!$category->current_candidate_id) continue;

            foreach ($judges as $judge) {
                $score = Score::where([
                    'event_id' => $this->event_id,
                    'category_id' => $category->category_id,
                    'candidate_id' => $category->current_candidate_id,
                    'judge_id' => $judge->judge_id,
                    'status' => 'confirmed',
                ])->first();

                if (!$score) {
                    $fullName = trim(($judge->user?->first_name ?? '') . ' ' . ($judge->user?->last_name ?? ''));
                    if ($fullName && !in_array($fullName, $pendingJudges)) {
                        $pendingJudges[] = $fullName;
                    }
                }
            }
        }

        return [
            'event_id' => $this->event_id,
            'event_name' => $this->event_name,
            'venue' => $this->venue,
            'start_date' => $this->start_date instanceof Carbon
                ? $this->start_date->toDateTimeString()
                : Carbon::parse($this->start_date)->toDateTimeString(),
            'end_date' => $this->end_date instanceof Carbon
                ? $this->end_date->toDateTimeString()
                : Carbon::parse($this->end_date)->toDateTimeString(),
            'status' => $this->status,
            'division' => $this->division,
            'cover_photo' => $this->getCoverPhotoUrl(),
            'description' => $this->description,
            'created_by' => $this->whenLoaded('createdBy', fn() => [
                'user_id' => $this->createdBy?->user_id,
                'first_name' => $this->createdBy?->first_name,
                'last_name' => $this->createdBy?->last_name,
            ]),
            'candidates_count' => $this->whenCounted('candidates', fn() => $this->candidates_count),
            'judges_count' => $this->whenCounted('judges', fn() => $this->judges_count),
            'categories_count' => $this->whenCounted('categories', fn() => $this->categories_count),
            'active_categories_count' => $this->categories()->where('status', 'active')->count(),
            'judges_with_pending_scores' => $pendingJudges,
            'statisticians' => $this->statisticians,
        ];
    }

    /**
     * Get the proper cover photo URL - handles both Cloudinary and legacy storage
     */
    private function getCoverPhotoUrl(): ?string
    {
        // Priority 1: New Cloudinary URL field
        if (!empty($this->cover_photo_url)) {
            return $this->cover_photo_url;
        }

        // Priority 2: Legacy cover_photo field
        if (!empty($this->cover_photo)) {
            // If it's already a full URL (starts with http), return as-is
            if (filter_var($this->cover_photo, FILTER_VALIDATE_URL)) {
                return $this->cover_photo;
            }

            // If it contains a domain, it's likely already a full URL
            if (str_contains($this->cover_photo, '.com') || str_contains($this->cover_photo, '.net') || str_contains($this->cover_photo, '.org')) {
                // Add https:// if it's missing
                if (!str_starts_with($this->cover_photo, 'http')) {
                    return 'https://' . $this->cover_photo;
                }
                return $this->cover_photo;
            }

            // Otherwise, treat as local storage path
            return asset('storage/' . ltrim($this->cover_photo, '/'));
        }

        return null;
    }
}