<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    protected $table = 'scores';

    public $incrementing = false;
    protected $primaryKey = null;
    protected $keyType = 'array';

    protected $fillable = [
        'judge_id',
        'candidate_id',
        'category_id',
        'event_id',
        'score',
        'comments',
        'status',
        'stage_id',
        'created_at',
        'updated_at',
    ];

    protected $attributes = [
        'score' => 1,
        'status' => 'temporary',
    ];

    protected $casts = [
        'score' => 'integer',
        'status' => 'string',
    ];

    public function judge()
    {
        return $this->belongsTo(Judge::class, 'judge_id', 'judge_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id', 'candidate_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'stage_id');
    }

    public function getKey()
    {
        return [
            'judge_id' => $this->judge_id,
            'candidate_id' => $this->candidate_id,
            'category_id' => $this->category_id,
            'event_id' => $this->event_id,
        ];
    }

    protected function setKeysForSaveQuery($query)
    {
        $keys = $this->getKey();
        foreach ($keys as $key => $value) {
            $query->where($key, '=', $value);
        }
        return $query;
    }

    public static function findByCompositeKey(array $attributes)
    {
        $query = static::query();
        foreach ($attributes as $key => $value) {
            $query->where($key, '=', $value);
        }
        return $query->first();
    }

    /**
     * Get the value used to identify the model for queue serialization.
     *
     * @return array
     */
    public function getQueueableId()
    {
        return $this->getKey();
    }

    /**
     * Get the relationships that should be serialized.
     *
     * @return array
     */
    public function getQueueableRelations()
    {
        return [];
    }

    /**
     * Resolve a model instance from a queueable ID.
     *
     * @param  mixed  $id
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public static function resolveFromQueueableId($id)
    {
        if (!is_array($id)) {
            return null;
        }

        return static::findByCompositeKey($id);
    }
}