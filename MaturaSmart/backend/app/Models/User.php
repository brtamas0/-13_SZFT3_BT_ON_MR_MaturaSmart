<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'streak_start' => 'datetime',
        'last_activity' => 'datetime',
    ];

    public function settings()
    {
        return $this->hasMany(UserSetting::class);
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }

    public function inventory()
    {
        return $this->hasMany(UserInventory::class);
    }

    public function achievements()
    {
        return $this->belongsToMany(Achievement::class, 'user_achievements')
            ->withPivot('earned_at');
    }
    
}
