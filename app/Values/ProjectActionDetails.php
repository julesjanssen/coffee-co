<?php

declare(strict_types=1);

namespace App\Values;

final class ProjectActionDetails extends CastableValueObject
{
    public ?bool $extraService = null;

    public ?string $hint = null;
}
