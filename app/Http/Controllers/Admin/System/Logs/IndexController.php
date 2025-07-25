<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System\Logs;

use App\Support\Logs\LogFilenameValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class IndexController
{
    public function index(Request $request)
    {
        return Inertia::render('system/logs/index', [
            'logFiles' => $this->listFiles(storage_path('logs')),
        ]);
    }

    private function listFiles(string $logPath)
    {
        if (! File::exists($logPath)) {
            return collect();
        }

        return collect(File::files($logPath))
            ->filter(fn($file) => $file->getSize() > 0)
            ->filter(fn($file) => str_contains($file->getFilename(), 'laravel-'))
            ->filter(fn($file) => LogFilenameValidator::isValidLogFilename($file->getFilename()))
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
