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

class ViewController
{
    public function view(Request $request, string $filename)
    {
        $logPath = LogFilenameValidator::validateLogFile($filename);

        $request->validate([
            'page' => ['sometimes', 'integer', 'min:1'],
        ]);

        $page = (int) $request->get('page', 1);

        $parser = new LogParser($logPath);
        $logPaginator = $parser->getPage($page);

        $fileInfo = [
            'name' => $filename,
            'size' => File::size($logPath),
            'modified' => Date::createFromTimestamp(File::lastModified($logPath)),
        ];

        return Inertia::render('system/logs/view', [
            'file' => $fileInfo,
            'logs' => LogEntryResource::collection($logPaginator),
            'links' => [
                'download' => route('admin.system.logs.download', [$filename]),
                'index' => route('admin.system.logs.index'),
            ],
        ]);
    }

    public function download(Request $request, string $filename)
    {
        $logPath = LogFilenameValidator::validateLogFile($filename);

        return response()->download($logPath, basename($logPath));
    }
}
