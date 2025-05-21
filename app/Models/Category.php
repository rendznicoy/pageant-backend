<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';
    protected $primaryKey = 'category_id';

    protected $fillable = [
        'event_id',
        'stage_id',
        'category_name',
        'status',
        'current_candidate_id',
        'category_weight',
        'max_score',
    ];

    protected $attributes = [
        'status' => 'pending',
        'category_weight' => 100,
        'max_score' => 100,
    ];

    protected $casts = [
        'category_weight' => 'integer',
        'max_score' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'stage_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'category_id', 'category_id');
    }

    public function hasPendingScores()
    {
        if (!$this->current_candidate_id) {
            Log::debug("No current candidate for category", [
                'category_id' => $this->category_id,
                'event_id' => $this->event_id,
            ]);
            return false;
        }

        $judgeCount = Judge::where('event_id', $this->event_id)->count();
        $confirmedScoreCount = Score::where('category_id', $this->category_id)
            ->where('event_id', $this->event_id)
            ->where('candidate_id', $this->current_candidate_id)
            ->where('status', 'confirmed')
            ->count();

        $hasTemporaryScores = Score::where('category_id', $this->category_id)
            ->where('event_id', $this->event_id)
            ->where('candidate_id', $this->current_candidate_id)
            ->where('status', 'temporary')
            ->exists();

        $hasPending = $confirmedScoreCount < $judgeCount || $hasTemporaryScores;

        Log::info("Pending scores check for category", [
            'category_id' => $this->category_id,
            'event_id' => $this->event_id,
            'current_candidate_id' => $this->current_candidate_id,
            'judge_count' => $judgeCount,
            'confirmed_scores_count' => $confirmedScoreCount,
            'has_temporary_scores' => $hasTemporaryScores,
            'has_pending_scores' => $hasPending,
        ]);

        return $hasPending;
    }
}