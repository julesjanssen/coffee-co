<?php

declare(strict_types=1);

namespace App\Http\Controllers\Game\BackOffice;

use Illuminate\Http\Request;

class ViewController
{
    public function view(Request $request)
    {
        return redirect()->route('game.backoffice.projects.index');
    }
}
