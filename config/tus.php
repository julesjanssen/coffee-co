<?php

declare(strict_types=1);

use App\Models\TusUpload;

return [
    /*
    |--------------------------------------------------------------------------
    | Max upload size
    |--------------------------------------------------------------------------
    |
    | This value controls the maximum filesize (in bytes)
    | that a file uploaded through tus is allowed to have.
    |
    */
    'max_upload_size' => 500 * 1024 * 1024,

    /*
    |--------------------------------------------------------------------------
    | Route prefix
    |--------------------------------------------------------------------------
    |
    | This value determines what prefix will be used for the
    | tus 'upload' route. Leave blank to default to /upload
    |
    */
    'route_prefix' => '',

    /*
    |--------------------------------------------------------------------------
    | Middleware
    |--------------------------------------------------------------------------
    |
    | Here you may specify which middlewares should be used
    | while processing requests for the tus endpoints.
    |
    */
    'middleware' => [\Spatie\Multitenancy\Http\Middleware\NeedsTenant::class],

    /*
    |--------------------------------------------------------------------------
    | Upload model
    |--------------------------------------------------------------------------
    |
    | This model will be used to save upload details.
    | It should extend the Vagebond\Tus\Models\Upload class
    |
    */
    'upload_model' => TusUpload::class,
];
