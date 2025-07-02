<?php

declare(strict_types=1);

namespace App\Support\Logs;

use Illuminate\Support\Facades\File;

class LogFilenameValidator
{
    public static function isValidLogFilename(string $filename): bool
    {
        return str_ends_with($filename, '.log') || (str_ends_with($filename, '.gz') && str_contains($filename, '.log'));
    }

    public static function validateLogFile(string $filename): string
    {
        $logPath = storage_path("logs/{$filename}");

        if (! File::exists($logPath)) {
            abort(404, 'Log file not found');
        }

        if (! self::isValidLogFilename($filename)) {
            abort(400, 'Invalid log file');
        }

        return $logPath;
    }
}
