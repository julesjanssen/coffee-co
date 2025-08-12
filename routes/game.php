<?php

declare(strict_types=1);

use App\Enums\Participant\Role;
use App\Http\Controllers\Game\Facilitator\Round\StatusController as FacilitatorRoundStatusController;
use App\Http\Controllers\Game\Facilitator\Session\SettingsController;
use App\Http\Controllers\Game\Facilitator\Session\StatusController as SessionStatusController;
use App\Http\Controllers\Game\Facilitator\StatusController;
use App\Http\Controllers\Game\LogoutController;
use App\Http\Controllers\Game\Sessions\IndexController;
use App\Http\Controllers\Game\Sessions\ViewController;
use App\Http\Controllers\Game\ViewController as GameViewController;
use App\Http\Middleware\Game\GameSession;
use App\Http\Middleware\Game\ParticipantRole;
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
])->namespace('Products')->prefix('products/')->as('products.')->group(function () {
    Route::get('{product}', 'ViewController@view')->name('view');
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::SALES_1, Role::SALES_2, Role::SALES_3]),
])->namespace('Sales')->prefix('sales/')->as('sales.')->group(function () {
    Route::get('/', 'ViewController@view')->name('view');

    Route::prefix('relation-visit/')
        ->namespace('RelationVisit')
        ->as('relation-visit.')->group(function () {
            Route::get('/', 'ViewController@view')->name('view');
            Route::post('{client}', 'ClientController@store');
            Route::get('{client}', 'ClientController@view')->name('client');
        });

    Route::prefix('request-visit/')
        ->namespace('RequestVisit')
        ->as('request-visit.')->group(function () {
            Route::get('/', 'ViewController@view')->name('view');
            Route::post('{client}', 'ClientController@store');
            Route::get('{client}', 'ClientController@view')->name('client');
            Route::post('{client}/quiz', 'QuizController@store');
            Route::get('{client}/quiz', 'QuizController@view')->name('quiz');
            Route::get('{client}/no-requests', 'NoRequestsController@view')->name('no-requests');
        });

    Route::prefix('projects/')
        ->namespace('Projects')
        ->as('projects.')->group(function () {
            Route::get('{project}', 'ViewController@view')->name('view');
        });
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::SALES_SCREEN]),
])->namespace('SalesScreen')->prefix('sales-screen/')->as('sales-screen.')->group(function () {
    Route::get('/', 'ProjectsController@view')->name('projects');
    Route::get('results', 'ResultsController@view')->name('results');
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::TECHNICAL_1, Role::TECHNICAL_2]),
])->namespace('Technical')->prefix('technical/')->as('technical.')->group(function () {
    Route::get('/', 'ViewController@view')->name('view');

    Route::prefix('maintenance/')
        ->namespace('Maintenance')
        ->as('maintenance.')->group(function () {
            Route::get('/', 'ViewController@view')->name('view');
            Route::post('{project}', 'Projects\UpdateController@store')->name('projects.update');
            Route::get('{project}/{action}', 'Projects\ActionController@view')->name('projects.action.view');
            Route::post('{project}/{action}/extra-service', 'Projects\ExtraServiceController@store');
            Route::get('{project}/{action}/extra-service', 'Projects\ExtraServiceController@view')->name('projects.action.extra-service');
        });

    Route::prefix('installation/')
        ->namespace('Installation')
        ->as('installation.')->group(function () {
            Route::get('/', 'ViewController@view')->name('view');
            Route::post('{project}', 'Projects\UpdateController@store')->name('projects.update');
        });
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::TECHNICAL_SCREEN]),
])->namespace('TechnicalScreen')->prefix('technical-screen/')->as('technical-screen.')->group(function () {
    Route::get('/', 'ProjectsController@view')->name('projects');
    Route::get('results', 'ResultsController@view')->name('results');
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::MARKETING_1]),
])->namespace('Marketing')->prefix('marketing/')->as('marketing.')->group(function () {
    Route::get('/', 'ViewController@view')->name('view');
    Route::get('results', 'ResultsController@view')->name('results');
    Route::get('info', 'InfoController@view')->name('info');

    Route::prefix('campaign/')
        ->namespace('Campaign')
        ->as('campaign.')->group(function () {
            Route::post('/', 'ViewController@store');
            Route::get('/', 'ViewController@view')->name('view');
        });

    Route::prefix('training/')
        ->namespace('Training')
        ->as('training.')->group(function () {
            Route::post('{type}', 'ViewController@store');
            Route::get('{type}', 'ViewController@view')->name('view');
        });

    Route::prefix('mmma/')
        ->namespace('Mmma')
        ->as('mmma.')->group(function () {
            Route::post('/', 'ViewController@store');
            Route::get('/', 'ViewController@view')->name('view');
        });
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::BACKOFFICE_1]),
])->namespace('BackOffice')->prefix('backoffice/')->as('backoffice.')->group(function () {
    Route::get('/', 'ViewController@view')->name('view');
    Route::get('results', 'ResultsController@view')->name('results');

    Route::prefix('projects/')
        ->namespace('Projects')
        ->as('projects.')->group(function () {
            Route::get('/', 'IndexController@index')->name('index');
            Route::post('{project}', 'ViewController@store');
            Route::get('{project}', 'ViewController@view')->name('view');
        });
});

Route::middleware([
    Authenticate::using('participant'),
    GameSession::class,
    ParticipantRole::roles([Role::MATERIALS_1]),
])->namespace('Materials')->prefix('materials/')->as('materials.')->group(function () {
    Route::get('/', 'ProjectsController@view')->name('projects');
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
