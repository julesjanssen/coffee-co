<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSending;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class AccountInvitation extends Notification implements ShouldQueue
{
    use Queueable;

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = URL::temporarySignedRoute('admin.auth.invite', now()->addDays(7), ['user' => $notifiable]);

        $subject = __('Your new account at :app', [
            'app' => config('app.title'),
        ]);

        return (new MailMessage())
            ->subject($subject)
            ->markdown('notifications.account.invitation', [
                'user' => $notifiable,
                'url' => $url,
            ]);
    }

    public function shouldLog(NotificationSending $event): bool
    {
        return true;
    }
}
