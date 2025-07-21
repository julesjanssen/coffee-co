<?php

declare(strict_types=1);

use App\Http\Controllers\Game\Sessions\IndexController;
use App\Http\Controllers\Game\Sessions\ViewController;
use App\Http\Controllers\Game\ViewController as GameViewController;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;

Route::middleware([
    Authenticate::using('participant,facilitator'),
])->get('logout', function (Request $request) {
    $user = $request->user();
    $session = null;

    if (! empty($user)) {
        $session = $user->session;
    }

    Auth::guard('participant')->logout();
    Auth::guard('facilitator')->logout();

    if (! empty($session)) {
        return redirect()->route('game.sessions.view', [$session->public_id]);
    }

    return redirect()->route('game.base');
});

Route::get('sessions', [IndexController::class, 'index'])->name('sessions.index');
Route::get('sessions/{session}', [ViewController::class, 'view'])->name('sessions.view');
Route::post('sessions/{session}', [ViewController::class, 'store']);

Route::middleware([
    Authenticate::using('participant,facilitator'),
])->group(function () {
    Route::get('/', [GameViewController::class, 'view'])->name('base');
});

Route::middleware([
    Authenticate::using('facilitator'),
])->group(function () {});
