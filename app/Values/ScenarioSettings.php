<?php

declare(strict_types=1);

namespace App\Values;

final class ScenarioSettings extends CastableValueObject
{
    public int $startYear {
        get => $this->startYear ?? (int) date('Y') + 1;
    }

    public int $years = 6;
}
