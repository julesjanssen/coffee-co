<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System\Logs;

use App\Http\Resources\Admin\LogEntryResource;
use App\Support\Logs\LogFilenameValidator;
use App\Support\Logs\LogParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class EntryController
{
    public function view(Request $request, string $filename, string $id)
    {
        $logPath = LogFilenameValidator::validateLogFile($filename);

        $parser = new LogParser($logPath);

        $entry = $parser->findById($id);

        if (! $entry) {
            abort(404, 'Log entry not found');
        }

        $fileInfo = [
            'name' => $filename,
            'size' => File::size($logPath),
            'modified' => Date::createFromTimestamp(File::lastModified($logPath)),
        ];

        return Inertia::render('system/logs/entry', [
            'file' => $fileInfo,
            'entry' => LogEntryResource::make($entry),
            'links' => [
                'view' => route('admin.system.logs.view', ['filename' => $filename]),
            ],
        ]);
    }
}
