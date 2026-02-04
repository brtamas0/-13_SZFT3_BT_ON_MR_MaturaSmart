<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalMessage extends Model
{
    use HasFactory;

    /**
     * A mezők, amiket tömegesen ki lehet tölteni (create metódussal).
     * Ez biztonsági okokból kötelező!
     */
    protected $fillable = [
        'title',
        'message',
        'type',        // info, warning, danger, success
        'is_active',   // true/false
        'expires_at',  // dátum
    ];

    /**
     * Adattípus konverziók.
     * Így a PHP-ban az is_active 'boolean' lesz, az expires_at pedig 'Carbon' dátum objektum.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];
}