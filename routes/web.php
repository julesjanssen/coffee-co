<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Spatie\LaravelPasskeys\Http\Controllers\AuthenticateUsingPasskeyController;
use Spatie\LaravelPasskeys\Http\Controllers\GeneratePasskeyAuthenticationOptionsController;

Route::namespace('\App\Http\Controllers\Admin')
    ->prefix('admin/')
    ->as('admin.')
    ->middleware(['auth'])
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
            Route::namespace('\Spatie\LaravelPasskeys\Http\Controllers')->group(function () {
                Route::get('passkeys/options/auth', GeneratePasskeyAuthenticationOptionsController::class);
                Route::post('passkeys/login', AuthenticateUsingPasskeyController::class);
            });
        });
    });

Route::get('/', fn() => ['success' => true]);
