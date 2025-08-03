<?php

declare(strict_types=1);

use App\Http\Controllers\Game\BackOffice\ViewController as BackOfficeViewController;
use App\Http\Controllers\Game\Facilitator\Round\StatusController as FacilitatorRoundStatusController;
use App\Http\Controllers\Game\Facilitator\Session\SettingsController;
use App\Http\Controllers\Game\Facilitator\Session\StatusController as SessionStatusController;
use App\Http\Controllers\Game\Facilitator\StatusController;
use App\Http\Controllers\Game\LogoutController;
use App\Http\Controllers\Game\Sales\RequestVisitController;
use App\Http\Controllers\Game\Sales\ViewController as SalesViewController;
use App\Http\Controllers\Game\Sessions\IndexController;
use App\Http\Controllers\Game\Sessions\ViewController;
use App\Http\Controllers\Game\ViewController as GameViewController;
use App\Http\Middleware\Game\GameSession;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;

Route::middleware([
    Authenticate::using('participant,facilitator'),
])->post('logout', [LogoutController::class, 'store'])->name('logout');

Route::get('sessions', [IndexController::class, 'index'])->name('sessions.index');
Route::get('sessions/{session}', [ViewController::class, 'view'])->name('sessions.view');
Route::post('sessions/{session}', [ViewController::class, 'store']);

Route::middleware([
    Authenticate::using('participant,facilitator'),
    GameSession::class,
])->group(function () {
    Route::get('/', [GameViewController::class, 'view'])->name('base');
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
])->prefix('sales/')->as('sales.')->group(function () {
    Route::get('/', [SalesViewController::class, 'view'])->name('view');
    Route::get('/relation-visit', [])->name('relation-visit');
    Route::get('/request-visit', [RequestVisitController::class, 'view'])->name('request-visit');
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
])->prefix('backoffice/')->as('backoffice.')->group(function () {
    Route::get('/', [BackOfficeViewController::class, 'view'])->name('view');
});

Route::middleware([
    Authenticate::using('facilitator'),
    GameSession::class,
])->prefix('facilitator/')->as('facilitator.')->group(function () {
    Route::get('/', [StatusController::class, 'view'])->name('status');
    Route::post('/session/settings', [SettingsController::class, 'store'])->name('session-settings');
    Route::post('/session/status', [SessionStatusController::class, 'store'])->name('session-status');
    Route::post('/round/status', [FacilitatorRoundStatusController::class, 'store'])->name('round-status');

    Route::get('/mmma', [StatusController::class, 'view'])->name('mmma');
});
