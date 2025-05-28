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
        'start_date',
        'end_date',
        'status',
        'created_by',
        'cover_photo', 
        'cover_photo_url',      // New: Store Cloudinary URL
        'cover_photo_public_id',
        'description',
        'division',
        'statisticians',
        'global_max_score',
    ];

    protected $attributes = [
        'status' => 'inactive',
        'global_max_score' => 100,
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'statisticians' => 'array',
        'global_max_score' => 'integer', 
        'cover_photo_url' => 'string',
        'cover_photo_public_id' => 'string',
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

    public function getMaxScoreAttribute()
    {
        return $this->global_max_score ?? 100;
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

    public function getCoverPhotoUrlAttribute()
    {
        if (!$this->cover_photo) {
            return null;
        }
        
        // If it's already a full URL, return as is
        if (str_starts_with($this->cover_photo, 'http')) {
            return $this->cover_photo;
        }
        
        // If it's a relative path, build the proper URL
        return url('storage/' . $this->cover_photo);
    }
}