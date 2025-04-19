<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'event_name',
        'event_code',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by', 'user_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'event_id');
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class, 'event_id');
    }

    public function judges()
    {
        return $this->hasMany(Judge::class, 'event_id');
    }
}
