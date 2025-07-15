<?php

declare(strict_types=1);

namespace App\Events;

use App\Models\GameSession;
use Illuminate\Queue\SerializesModels;

class GameSessionCreated
{
    use SerializesModels;

    public function __construct(public GameSession $session) {}
}
