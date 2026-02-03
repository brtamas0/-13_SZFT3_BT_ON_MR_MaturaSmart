<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        ResetPassword::toMailUsing(function ($notifiable, $token) {
            
            $frontendUrl = env('FRONTEND_URL', 'http://maturasmart.hu');
            $url = "{$frontendUrl}/reset-password/{$token}?email={$notifiable->getEmailForPasswordReset()}";
            return (new MailMessage)
                ->subject('Jelszó visszaállítása - MaturaSmart')
                ->view('emails.reset-password', ['url' => $url]);
        });
    }
}

