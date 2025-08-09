<?php

declare(strict_types=1);

namespace App\Values;

final class GameCampaignCodeDetails extends CastableValueObject
{
    /** @var string[]|null */
    public ?array $hints = null;
}
