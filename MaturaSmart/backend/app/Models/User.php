<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // Ha API-t használunk majd

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guarded = []; // Minden mező tölthető

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'streak_start' => 'datetime',
        'last_activity' => 'datetime',
    ];

    // --- KAPCSOLATOK ---

    // Egy usernek sok beállítása van
    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    // Egy usernek sok megoldott feladata van
    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    // Egy usernek sok tárgya van a leltárban
    public function inventory()
    {
        return $this->hasMany(UserInventory::class);
    }

    // Egy usernek sok kitüntetése van
    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
                    ->withPivot('earned_at');
    }
}