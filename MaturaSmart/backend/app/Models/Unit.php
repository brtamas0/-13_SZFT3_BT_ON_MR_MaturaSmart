<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function topics()
    {
        return $this->hasMany(Topic::class)->orderBy('year', 'asc');
    }
}