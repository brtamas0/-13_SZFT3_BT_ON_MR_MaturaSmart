<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = env('FRONTEND_URL', 'http://maturasmart.hu') . "/reset-password/{$this->token}?email={$notifiable->getEmailForPasswordReset()}";

        return (new MailMessage)
            ->subject('Jelszó visszaállítása - MaturaSmart') // Tárgy
            ->greeting('Szia!') // Megszólítás
            ->line('Azért kaptad ezt a levelet, mert jelszó-visszaállítási kérelem érkezett a fiókodhoz.')
            ->action('Jelszó Visszaállítása', $url) // Gomb
            ->line('Ez a link 60 percig érvényes.')
            ->line('Ha nem te kérted a visszaállítást, hagyd figyelmen kívül ezt az emailt.')
            ->salutation('Üdvözlettel, A MaturaSmart Csapata');
    }
}