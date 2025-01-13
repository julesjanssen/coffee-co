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

        return Cache::remember(__METHOD__, $ttl, fn() => collect([
            $this->getDirectoryStats(storage_path('framework/views'), 'view cache'),
            $this->getDatabaseSize('sessions', 'sessions'),
            $this->getDatabaseSize('cache', 'cache'),
            $this->getDirectoryStats(storage_path('logs'), 'logs'),
            $this->getDirectoryStats(storage_path('tmp'), 'temporary files'),
        ])->filter()->values());
    }

    private function getDatabaseSize(string $dbName, string $name)
    {
        $config = config('database.connections.' . $dbName);
        if (empty($config) || $config['driver'] !== 'sqlite') {
            return;
        }

        $path = $config['database'];
        if (! file_exists($path)) {
            return;
        }

        return (object) [
            'name' => $name,
            'size' => filesize($path),
        ];
    }

    private function getDirectoryStats(string $path, string $name)
    {
        if (! file_exists($path)) {
            return;
        }

        $finder = (new Finder())
            ->files()
            ->in($path);

        $size = 0;
        foreach ($finder as $file) {
            $size += $file->getSize();
        }

        return (object) [
            'name' => $name,
            'size' => $size,
            'fileCount' => count($finder),
        ];
    }

    public function toArray()
    {
        return [
            'attachments' => $this->getAttachmentsSize(),
            'appStorage' => $this->getAppStorageStats(),
        ];
    }
}
