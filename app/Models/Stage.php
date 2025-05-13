<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stage extends Model
{
    use HasFactory;

    protected $table = 'stages';

    protected $primaryKey = 'stage_id';

    protected $fillable = [
        'event_id',
        'stage_name',
        'status', // Add status to fillable
        'top_candidates_count',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'stage_id');
    }
}