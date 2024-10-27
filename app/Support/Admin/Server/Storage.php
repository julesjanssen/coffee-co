<?php

declare(strict_types=1);

namespace App\Support\Admin\Server;

use App\Models\Attachment;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Symfony\Component\Finder\Finder;

class Storage implements Arrayable
{
    private function getAttachmentsSize()
    {
        $ttl = Date::parse('+30 minutes');

        return Cache::remember(__METHOD__, $ttl, function () {
            $result = Attachment::query()
                ->selectRaw('SUM(filesize) as filesize_sum')
                ->selectRaw('SUM(filesize_disk) as filesize_disk_sum')
                ->toBase()
                ->first();

            return (object) [
                'size' => $result->filesize_sum,
                'disk' => $result->filesize_disk_sum,
            ];
        });
    }

    private function getAppStorageStats()
    {
        $ttl = Date::parse('+30 minutes');

        return Cache::remember(__METHOD__, $ttl, function () {
            $folders = [
                'framework/cache' => 'app cache',
                'framework/views' => 'view cache',
                'framework/sessions' => 'sessions',
                'logs' => 'logs',
                'tmp' => 'temporary files',
            ];

            $results = collect();

            foreach ($folders as $folder => $name) {
                $path = storage_path($folder);
                if (! file_exists($path)) {
                    continue;
                }

                $finder = (new Finder())
                    ->files()
                    ->in($path);

                $size = 0;
                foreach ($finder as $file) {
                    $size += $file->getSize();
                }

                $results->push((object) [
                    'folder' => $folder,
                    'size' => $size,
                    'count' => count($finder),
                    'name' => $name,
                ]);
            }

            return $results;
        });
    }

    public function toArray()
    {
        return [
            'attachments' => $this->getAttachmentsSize(),
            'appStorage' => $this->getAppStorageStats(),
        ];
    }
}
