<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = [
        'topic_id',
        'title',
        'url',
        'order'
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }
}