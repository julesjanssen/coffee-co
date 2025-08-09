<?php

declare(strict_types=1);

namespace App\Enums\Client;

use App\Models\ScenarioClient;
use App\Traits\EnumHelpers;

enum Segment: string
{
    use EnumHelpers;

    case TECHNICAL_SPECS = 'technical-specs';

    case EFFICIENCY = 'efficiency';

    case TOTAL_SOLUTION = 'total-solution';

    public function getHintMessageForClient(ScenarioClient $client)
    {
        return match ($this) {
            self::TECHNICAL_SPECS => __('The owner of :name focuses on technical specifications.', ['name' => $client->title]),
            self::EFFICIENCY => __('At :name, they prefer an efficient solution.', ['name' => $client->title]),
            self::TOTAL_SOLUTION => __('The people at :name want a total solution.', ['name' => $client->title]),
        };
    }
}
