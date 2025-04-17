<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $table = 'categories';

    protected $primaryKey = 'category_id';

    protected $fillable = [
        'event_id',
        'category_name',
        'category_weight',
        'max_score',
    ];

    protected $casts = [
        'category_weight' => 'integer',
        'max_score' => 'integer',
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
