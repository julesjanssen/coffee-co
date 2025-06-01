<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System\Logs;

use App\Http\Resources\Admin\LogEntryResource;
use App\Support\Logs\LogParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class ViewController
{
    public function view(Request $request, string $filename)
    {
        $logPath = storage_path("logs/{$filename}");

        if (! File::exists($logPath)) {
            abort(404, 'Log file not found');
        }

        if (! str_ends_with($filename, '.log')) {
            abort(400, 'Invalid log file');
        }

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
                'index' => route('admin.system.logs.index'),
            ],
        ]);
    }
}
