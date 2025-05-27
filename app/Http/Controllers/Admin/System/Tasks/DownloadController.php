<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System\Tasks;

use App\Models\SystemTask;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DownloadController
{
    public function download(Request $request, SystemTask $task)
    {
        if (! $task->isCompleted()) {
            throw new NotFoundHttpException();
        }

        if ($task->isExpired()) {
            throw new NotFoundHttpException();
        }

        $filename = $task->result['filename'] ?? null;
        if (empty($filename)) {
            throw new NotFoundHttpException();
        }

        return $task->storage()->download($filename);
    }
}
