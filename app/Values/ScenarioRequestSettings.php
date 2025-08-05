<?php

declare(strict_types=1);

namespace App\Values;

use App\Enums\Project\Status;
use App\Enums\Request\CompetitionLevel;

final class ScenarioRequestSettings extends CastableValueObject
{
    public int $value = 100;

    public int $competitionprice = 100;

    public CompetitionLevel $competitionlevel = CompetitionLevel::MEDIUM;

    public int $initialfailurechance = 0;

    public ?string $labconsultinginformation = null;

    /**
     * only applicable for requests with delay = 0
     */
    public Status $initialstatus = Status::ACTIVE;
}
