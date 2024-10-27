<?php

declare(strict_types=1);

return [
    'healthcheck' => [
        'token' => env('HEALTHCHECK_TOKEN'),
    ],

    'path' => getenv('PATH'),
];
