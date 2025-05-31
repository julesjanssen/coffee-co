<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Models\SystemTask;
use App\Support\SystemTasks\ExportUsers;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StyleguideController
{
    public function index()
    {
        return Inertia::render('system/styleguide', [
            'icons' => $this->icons(),
        ]);
    }

    public function store(Request $request)
    {
        return SystemTask::dispatch(new ExportUsers());
    }

    private function icons()
    {
        return collect(glob(resource_path('admin/icons/*.svg')))
            ->map(fn($filename) => basename((string) $filename, '.svg'));
    }
}
