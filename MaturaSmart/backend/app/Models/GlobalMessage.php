<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'type',        // info, warning, danger, success
        'is_active',   // true/false
        'expires_at',  // dátum
    ];


    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];
}