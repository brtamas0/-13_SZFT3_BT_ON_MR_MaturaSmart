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

    public function updateStreak()
    {
        $now = now();
        $lastActivity = $this->last_activity;

        if (!$lastActivity) {
            $this->update([
                'current_streak' => 1,
                'streak_start' => $now,
                'last_activity' => $now,
            ]);
            return;
        }

        if ($lastActivity->isSameDay($now)) {
            $this->update(['last_activity' => $now]);
            return;
        }

        if ($lastActivity->isYesterday()) {
            $this->update([
                'current_streak' => $this->current_streak + 1,
                'last_activity' => $now,
            ]);
            return;
        }

        if ($lastActivity->lt($now->subDay())) {
            $this->update([
                'lost_streak' => $this->lost_streak + 1,
                'current_streak' => 1,
                'streak_start' => $now,
                'last_activity' => $now,
            ]);
        }
    }
}
