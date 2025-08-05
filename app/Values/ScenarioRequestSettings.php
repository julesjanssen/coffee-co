<?php

declare(strict_types=1);

namespace App\Values;

final class ScenarioRequestSettings extends CastableValueObject
{
    public int $value = 100;

    public int $competitionprice = 100;

    public int $initialfailurechance = 0;
}
