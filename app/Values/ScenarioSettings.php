<?php

declare(strict_types=1);

namespace App\Values;

final class ScenarioSettings extends CastableValueObject
{
    public int $startYear = 2001;

    public int $years = 6;

    public int $targetMarketingCampaigns = 3;

    public int $targetBackofficeQuotes = 7;

    public int $targetTechnicalUptime = 85;

    public int $targetSalesRequests = 7;
}
