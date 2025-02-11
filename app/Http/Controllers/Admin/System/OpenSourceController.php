<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use Illuminate\Http\Request;
use Inertia\Inertia;

class OpenSourceController
{
    public function index(Request $request)
    {
        return Inertia::render('system/opensource', [
            'packages' => $this->listPackages(),
        ]);
    }

    private function listPackages()
    {
        $path = storage_path('app/open-source.json');

        if (! file_exists($path)) {
            return collect();
        }

        $data = json_decode(file_get_contents($path), true);

        return collect($data);
    }
}
