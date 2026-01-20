<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $guarded = [];

    public function units()
    {
        return $this->hasMany(Unit::class)->orderBy('order', 'asc');
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }
}