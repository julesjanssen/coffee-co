<?php

declare(strict_types=1);

namespace App\Listeners\GameSession;

use App\Enums\Participant\Role;
use App\Events\GameSessionCreated;
use App\Models\GameFacilitator;
use App\Models\GameParticipant;
use App\Models\GameSession;
use Illuminate\Support\Str;

class CreateParticipants
{
    public function handle(GameSessionCreated $event)
    {
        $session = $event->session;

        Role::collect()->each(fn($role) => $this->createParticipant($session, $role));

        GameFacilitator::create([
            'game_session_id' => $session->id,
            'code' => Str::lower(Str::random(6)),
        ]);
    }

    private function createParticipant(GameSession $session, Role $role)
    {
        GameParticipant::create([
            'game_session_id' => $session->id,
            'role' => $role,
        ]);
    }
}
