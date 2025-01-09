<?php

declare(strict_types=1);

use App\Http\Middleware\Admin\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/health'
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->web(
                append: [
                    \Spatie\Multitenancy\Http\Middleware\NeedsTenant::class,
                    \Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession::class,
                    HandleInertiaRequests::class,
                ]
            )
            ->trustHosts(null, true);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (App::environment('local', 'testing')) {
            // Show full responses in development
            $exceptions->dontTruncateRequestExceptions();
        } else {
            // Limit response size in production
            $exceptions->truncateRequestExceptionsAt(500);
        }

        // Log full responses regardless of environment
        $exceptions->reportable(function (RequestException $e) {
            Log::error('API Request Failed', [
                'uri' => $e->response->effectiveUri(),
                'status' => $e->response->status(),
                'body' => $e->response->body(),
            ]);
        });
    })
    ->create();
