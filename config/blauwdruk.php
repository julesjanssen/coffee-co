<?php

declare(strict_types=1);

return [
    'healthcheck' => [
        'token' => env('HEALTHCHECK_TOKEN'),
    ],

    'path' => getenv('PATH') ?: '/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin:/snap/bin:/opt/homebrew/bin',
];
