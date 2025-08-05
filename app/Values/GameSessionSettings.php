<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\GameSession\Flow;
use InvalidArgumentException;

final class GameSessionSettings extends CastableValueObject
{
    public bool $shouldPauseAfterCurrentRound = false;

    public int $failChanceIncreasePerRound = 2;

    public int $maxProjectsPerClientPerYear = 2;

    public int $secondsPerRound = 10;

    public int $roundsToSubmitOffer = 10;

    public int $roundsToDeliverProject = 6;

    public int $costLabconsultingRequestVisit = 5;

    public int $costLabconsultingOffer = 25;

    public Flow $flow = Flow::MEDIUM;

    public int $clientNpsStart {
        set(?int $clientNpsStart) {
            if (is_null($clientNpsStart)) {
                $clientNpsStart = 60;
            }

            if ($clientNpsStart < 0 || $clientNpsStart > 100) {
                throw new InvalidArgumentException('Client NPS start should be between 0 & 100');
            }

            $this->clientNpsStart = $clientNpsStart;
        }

        get => $this->clientNpsStart ?? 60;
    }
}
