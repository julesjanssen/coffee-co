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

Route::get('/', fn() => ['success' => true]);
