<?php

declare(strict_types=1);

return [
    'build' => [
        'commands' => [
            'yarn run admin:build',
            'yarn run front:build',
        ],

        'env' => [
            'NODE_ENV' => 'production',
        ],

        'compression' => [
            'enabled' => true,
        ],
    ],

    'werkplaats' => [
        'base_uri' => env('WERKPLAATS_BASE_URI'),
        'project_id' => env('WERKPLAATS_PROJECT_ID'),
    ],

    'deploy' => [
        'webhook' => env('AANNEMER_DEPLOY_WEBHOOK'),
    ],
];
