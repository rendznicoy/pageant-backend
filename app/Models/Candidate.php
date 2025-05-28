<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Candidate extends Model
{
    use HasFactory;

    protected $table = 'candidates';

    protected $primaryKey = 'candidate_id';

    protected $fillable = [
        'first_name',
        'last_name',
        'event_id',
        'candidate_number',
        'sex',
        'team',
        'photo_url',        // New: Cloudinary URL
        'photo_public_id',  // New: Cloudinary public_id
        'is_active',
    ];

    protected $cast = [
        'candidate_number' => 'integer',
        'is_active' => 'boolean',
        'photo_url' => 'string',
        'photo_public_id' => 'string',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'candidate_id');
    }

    public function getPhotoUrlAttribute()
    {
        if (is_string($this->photo) && Storage::exists($this->photo)) {
            return Storage::url($this->photo); // For uploaded images
        }

        // Optional fallback: base64 encode if BLOB
        if ($this->photo) {
            $base64 = base64_encode($this->photo);
            return "data:image/jpeg;base64,{$base64}";
        }

        return null;
    }
}
