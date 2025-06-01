<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System\Logs;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        $logPath = storage_path('logs');

        return Inertia::render('system/logs/index', [
            'logFiles' => $this->listFiles($logPath),
            'logPath' => $logPath,
        ]);
    }

    private function listFiles(string $logPath)
    {
        if (! File::exists($logPath)) {
            return collect();
        }

        return collect(File::files($logPath))
            ->filter(fn($file) => str_ends_with($file->getFilename(), '.log'))
            ->filter(fn($file) => str_contains($file->getFilename(), 'laravel-'))
            ->map(fn($file) => [
                'name' => $file->getFilename(),
                'path' => $file->getPathname(),
                'size' => $file->getSize(),
                'modified' => Date::createFromTimestamp($file->getMTime()),
                'links' => [
                    'view' => route('admin.system.logs.view', ['filename' => $file->getFilename()]),
                ],
            ])
            ->sortByDesc('modified')
            ->values();
    }
}
