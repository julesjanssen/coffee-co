<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\GameSession\Flow;

final class GameSessionSettings extends CastableValueObject
{
    public bool $shouldPauseAfterCurrentRound = false;

    public int $failChanceIncreasePerRound = 2;

    public int $maxProjectsPerClientPerYear = 2;

    public int $secondsPerRound = 120;

    public int $roundsToSubmitOffer = 10;

    public int $roundsToDeliverProject = 6;

    public int $costLabconsultingRequest = 5;

    public int $costLabconsultingOffer = 25;

    public int $hdmaRefreshRoundCooldown = 4;

    public int $hdmaEnabledRoundCount = 6;

    public int $hdmaEffectiveRoundCount = 6;

    public Flow $flow = Flow::MEDIUM;

    public int $clientNpsStart {
        set(int $clientNpsStart) {
            $this->clientNpsStart = min(100, max(0, (int) $clientNpsStart));
        }

        get => $this->clientNpsStart ?? 60;
    }
}
