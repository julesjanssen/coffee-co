<?php

declare(strict_types=1);

use App\Models\Attachment;

return [

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may specify which middlewares should be used
    | while processing requests for the bijlagen endpoints.
    |
    */
    'middleware' => [],

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | Specificy how you would like to prefix the
    | routes provided by this package.
    |
    */
    'prefix' => null,

    /*
    |--------------------------------------------------------------------------
    | Attachment model
    |--------------------------------------------------------------------------
    |
    | This model will be used to save attachment details.
    | It should implement the Vagebond\Bijlagen\Models\Attachment
    |
    */
    'attachment_model' => Attachment::class,
];
