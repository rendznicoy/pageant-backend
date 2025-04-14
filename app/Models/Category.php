<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'category_name',
        'event_id',
        'category_weight',
        'max_score',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function scores()
    {
        return $this->hasMany(Score::class, 'category_id');
    }
}
