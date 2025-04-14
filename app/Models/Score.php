<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Score extends Model
{
    use HasFactory;

    public $incrementing = false; // Required for composite PK
    protected $primaryKey = ['judge_id', 'candidate_id', 'category_id'];

    protected $fillable = [
        'judge_id',
        'candidate_id',
        'category_id',
        'score',
    ];

    public function judge()
    {
        return $this->belongsTo(Judge::class, 'judge_id');
    }

    public function candidate()
    {
        return $this->belongsTo(Candidate::class, 'candidate_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
