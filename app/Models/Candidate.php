<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    use HasFactory;

    protected $primaryKey = 'candidate_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'event_id',
        'candidate_number',
        'sex',
        'team',
        'photo',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'candidate_id');
    }
}
