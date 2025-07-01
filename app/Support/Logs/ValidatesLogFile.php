<?php

declare(strict_types=1);

namespace App\Support\Logs;

use Illuminate\Support\Facades\File;

trait ValidatesLogFile
{
    protected function validateLogFile(string $filename): string
    {
        $logPath = storage_path("logs/{$filename}");

        if (! File::exists($logPath)) {
            abort(404, 'Log file not found');
        }

        if (! str_ends_with($filename, '.log') && ! str_ends_with($filename, '.log.gz')) {
            abort(400, 'Invalid log file');
        }

        return $logPath;
    }
}
