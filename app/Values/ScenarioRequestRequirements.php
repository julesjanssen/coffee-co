<?php

declare(strict_types=1);

namespace App\Values;

final class ScenarioRequestRequirements extends CastableValueObject
{
    public bool $hdma = false;

    public bool $labconsulting = false;

    public int $tresholdNps = 50;

    public int $tresholdMarketing = 50;

    public int $customerdecision = 50;
}
