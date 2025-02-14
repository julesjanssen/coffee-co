<?php

declare(strict_types=1);

use App\Http\Middleware\Admin\HandleInertiaRequests;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Spatie\Multitenancy\Http\Middleware\EnsureValidTenantSession;
use Spatie\Multitenancy\Http\Middleware\NeedsTenant;

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
                    NeedsTenant::class,
                    EnsureValidTenantSession::class,
                    HandleInertiaRequests::class,
                ]
            )
            ->trustHosts(null, true);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        if (! App::runningInConsole()) {
            $request = request();

            $exceptions->context(fn() => array_filter([
                'url' => $request->url(),
                'ip' => $request->ip(),
                'userId' => $request->user()?->id,
                'userEmail' => $request->user()?->email,
            ]));
        }

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
