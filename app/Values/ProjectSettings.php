<?php

declare(strict_types=1);

namespace App\Values;

final class ProjectSettings extends CastableValueObject
{
    public bool $labConsultingApplied = false;

    public bool $labConsultingIncluded = false;

    public int $uptimeBonus = 0;

    /** @var array<array-key, string> $wonLostReasons */
    public array $wonLostReasons = [];
}
