<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

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
        });
    });

Route::get('/', fn() => ['success' => true]);
