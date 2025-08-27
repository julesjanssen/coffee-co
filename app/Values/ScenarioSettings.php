<?php

declare(strict_types=1);

namespace App\Values;

final class ScenarioSettings extends CastableValueObject
{
    public int $startYear = 2001;

    public int $years = 6;
}
