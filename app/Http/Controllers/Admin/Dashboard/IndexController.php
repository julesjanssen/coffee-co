<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\Dashboard;

class IndexController
{
    public function index()
    {
        return redirect()->route('admin.game-sessions.index');
    }
}
