<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System\Tasks;

use App\Models\SystemTask;
use Illuminate\Http\Request;

class ViewController
{
    public function view(Request $request, SystemTask $task)
    {
        return $task;
    }
}
