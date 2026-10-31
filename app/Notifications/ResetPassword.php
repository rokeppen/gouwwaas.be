<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Dutch password reset e-mail. With $newAccount it doubles as the invitation
 * for accounts created by an admin.
 */
class ResetPassword extends BaseResetPassword
{
    public function __construct(#[\SensitiveParameter] $token, public bool $newAccount = false)
    {
        parent::__construct($token);
    }

    public function toMail($notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);
        $hours = intdiv(config('auth.passwords.'.config('auth.defaults.passwords').'.expire'), 60);

        if ($this->newAccount) {
            return (new MailMessage)
                ->subject('Je account op de website van Gouw Waas')
                ->greeting('Hallo '.$notifiable->name.'!')
                ->line('Er is een account voor je aangemaakt op de website van Gouw Waas.')
                ->line('Kies via onderstaande knop een wachtwoord om in te loggen.')
                ->action('Wachtwoord instellen', $url)
                ->line("Deze link is {$hours} uur geldig.")
                ->salutation('Groetjes, Gouw Waas');
        }

        return (new MailMessage)
            ->subject('Wachtwoord opnieuw instellen')
            ->greeting('Hallo '.$notifiable->name.'!')
            ->line('Je ontvangt deze e-mail omdat er een nieuw wachtwoord is aangevraagd voor je account op de website van Gouw Waas.')
            ->action('Nieuw wachtwoord kiezen', $url)
            ->line("Deze link is {$hours} uur geldig.")
            ->line('Heb je dit niet zelf aangevraagd en kan je nog gewoon inloggen? Dan hoef je niets te doen.')
            ->salutation('Groetjes, Gouw Waas');
    }
}
