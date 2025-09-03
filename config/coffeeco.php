<?php

declare(strict_types=1);

use App\Enums\Locale;

return [

    /*
    |--------------------------------------------------------------------------
    | Annual Operational Costs
    |--------------------------------------------------------------------------
    |
    | This array defines the operational costs for each year of the game
    | simulation. Values are in million of currency units and represent
    | the base operational expenses that participants must account for
    | when making business decisions during gameplay.
    |
    */

    'operational_cost_per_year' => [
        350,
        350,
        500,
        700,
        900,
        1100,
    ],

    /*
    |--------------------------------------------------------------------------
    | HDMA Cost
    |--------------------------------------------------------------------------
    |
    | The cost associated with HDMA (Hot Drink Market Access)
    | activities. This value is in million of currency units and represents
    | the fixed cost participants must consider when implementing HDMA
    | strategies during the game simulation.
    |
    */

    'hdma_cost' => 75,

    /*
    |--------------------------------------------------------------------------
    | Scenario Groups
    |--------------------------------------------------------------------------
    |
    | Configuration for scenario groups that define different game scenarios.
    | Each group contains a base_id for identification, locale for language
    | support, and a human-readable title. These groups are used to organize
    | and categorize different gameplay scenarios within the application.
    |
    */

    'scenario_groups' => [
        [
            'base_id' => 'bseK5sxtSbMryjormsw',
            'locale' => Locale::EN,
            'title' => 'Original',
        ],
        [
            'base_id' => 'bsemwezwcZwck2STu1y',
            'locale' => Locale::NL,
            'title' => 'Original',
        ],
        // [
        //     'base_id' => 'bseZRsQNk6sfpLW2WCn',
        //     'locale' => Locale::IT,
        //     'title' => 'Original',
        // ],
    ],
];
