<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Events\NotificationSending;

class ResetPassword extends Notification implements ShouldQueue
{
    use Queueable;

    public function shouldLog(NotificationSending $event): bool
    {
        return true;
    }
}
