<?php

declare(strict_types=1);

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
];
