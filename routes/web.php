<?php

declare(strict_types=1);

use App\Http\Controllers\Admin\Auth\AuthenticateUsingPasskeyController;
use App\Http\Middleware\Game\HandleInertiaRequests;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Inertia\EncryptHistoryMiddleware;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController;

Authenticate::redirectUsing(function (Request $request) {
    if (Str::startsWith($request->uri()->path(), 'game')) {
        return '/game/sessions';
    }

    return '/auth/login';
});

Route::namespace('\App\Http\Controllers\Game')
    ->prefix('game/')
    ->as('game.')
    ->middleware([
        HandleInertiaRequests::class,
        // Delay::class,
    ])->group(function () {
        require __DIR__ . '/game.php';
    });

Route::namespace('\App\Http\Controllers\Admin')
    ->prefix('admin/')
    ->as('admin.')
    ->middleware([
        'auth',
        EncryptHistoryMiddleware::class,
    ])
    ->group(function () {
        require __DIR__ . '/admin.php';
    });

Route::namespace('\App\Http\Controllers\Admin\Auth')
    ->domain(config('fortify.domain', null))
    ->prefix(config('fortify.prefix'))
    ->middleware(config('fortify.middleware', ['web']))
    ->as('admin.auth.')
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::post('invite/{user}', 'InviteController@store');
            Route::get('invite/{user}', 'InviteController@view')->name('invite');
            Route::post('passkeys/login', AuthenticateUsingPasskeyController::class);

            Route::namespace('\Spatie\LaravelPasskeys\Http\Controllers')->group(function () {
                Route::get('passkeys/options/auth', GeneratePasskeyAuthenticationOptionsController::class);
            });
        });
    });

Route::redirect('/', '/game');
