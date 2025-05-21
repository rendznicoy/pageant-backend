<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Event extends Model
{
    use HasFactory;

    protected $table = 'events';

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'event_name',
        'venue', // Added
        'event_code',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'last_accessed',
        'is_starred',
        'cover_photo',
        'description',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'last_accessed' => 'datetime',
    ];

    public function getStartDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->toISOString() : null;
    }

    public function getEndDateAttribute($value)
    {
        return $value ? Carbon::parse($value)->toISOString() : null;
    }

    public function getCreatedAtAttribute($value)
    {
        return Carbon::parse($value)->toISOString();
    }

    public function getUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->toISOString();
    }

    public function getLastAccessedAttribute($value)
    {
        return $value ? Carbon::parse($value)->toISOString() : null;
    }

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

    public function stages()
    {
        return $this->hasMany(Stage::class, 'event_id');
    }

    protected function serializeDate(\DateTimeInterface $date)
    {
        return $date->format('Y-m-d\TH:i:s.u\Z');
    }
}