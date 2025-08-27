<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\Marketing;

use Illuminate\Http\Request;
use Inertia\Inertia;

class ResultsController
{
    public function view(Request $request)
    {
        $participant = $request->participant();
        $session = $participant->session;

        return Inertia::render('game/marketing/results', [
        ]);
    }
}
