<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Judge extends Model
{
    use HasFactory;

    protected $table = 'judges';

    protected $primaryKey = 'judge_id';

    protected $fillable = [
        'user_id',
        'event_id',
        'pin_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'judge_id');
    }
}
