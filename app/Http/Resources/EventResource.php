<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Models\Judge;
use App\Models\Score;
use Illuminate\Support\Facades\Log;

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
            'cover_photo' => $this->cover_photo_url, // Use Cloudinary URL
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

    private function getCoverPhotoUrl()
    {
        if (!$this->cover_photo) {
            return null;
        }

        // Debug info
        Log::info('Cover photo debug', [
            'cover_photo' => $this->cover_photo,
            'app_url' => config('app.url'),
            'environment' => app()->environment(),
            'storage_url' => Storage::url($this->cover_photo),
        ]);
        
        // Check if it's already a full URL
        if (filter_var($this->cover_photo, FILTER_VALIDATE_URL)) {
            return $this->cover_photo;
        }
        
        // Generate URL with proper scheme
        $url = Storage::url($this->cover_photo);
        
        // Force HTTPS in production
        if (app()->environment('production') && str_starts_with($url, 'http://')) {
            $url = str_replace('http://', 'https://', $url);
        }
        
        return $url;
    }
}