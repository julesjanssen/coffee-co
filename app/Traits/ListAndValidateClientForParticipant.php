<?php

declare(strict_types=1);

namespace App\Traits;

use App\Models\GameParticipant;
use App\Models\ScenarioClient;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

trait ListAndValidateClientForParticipant
{
    protected function validateClientForParticipant(GameParticipant $participant, ScenarioClient $client)
    {
        $clients = $this->listClientsForParticipant($participant);

        if (! $clients->contains($client)) {
            throw new BadRequestHttpException();
        }
    }

    protected function listClientsForParticipant(GameParticipant $participant)
    {
        return $participant->session->scenario->clients()
            ->where('player_id', '=', $participant->role->playerID())
            ->get();
    }
}
