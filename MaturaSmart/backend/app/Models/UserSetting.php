<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    protected $guarded = [];
    
    public $timestamps = false; 

    public $incrementing = false;

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}