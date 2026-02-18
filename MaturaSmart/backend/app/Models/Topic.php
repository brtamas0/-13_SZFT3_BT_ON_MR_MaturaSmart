<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = [
        'subject_id',
        'unit_id',
        'title',
        'slug',
        'description',
        'content',
        'type',
        'xp',
        'time_limit_minutes',
        'passing_percentage',
        'year',
        'year_label',
        'order',
        'reading_weight'
    ];
    
    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'xp' => 'integer',
        'reading_weight' => 'integer',
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function flashcards()
    {
        return $this->hasMany(Flashcard::class);
    }
    
    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function videos()
    {
        return $this->hasMany(Video::class)->orderBy('order', 'asc');
    }
}