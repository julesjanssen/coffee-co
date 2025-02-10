<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Enums\Disk;
use Aws\Exception\CredentialsException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use InvalidArgumentException;
use League\Flysystem\FileAttributes;
use League\Flysystem\StorageAttributes;
use League\Flysystem\UnableToListContents;

class DatabaseController
{
    public function index(Request $request)
    {
        $encryptionKey = config('backup.backup.password');

        $stats = $this->getDatabaseStats();

        return Inertia::render('system/database', [
            'variables' => $this->listDatabaseVariables(),
            'dbRows' => Arr::get($stats, 'rows', 0),
            'dbSize' => Arr::get($stats, 'size', 0),
            'hasEncryption' => ! empty($encryptionKey),
            'backups' => Inertia::defer(fn() => $this->listBackups()->values()),
        ]);
    }

    public function download(Request $request, $name)
    {
        $backup = $this->getBackupFromName($name);
        $disk = $this->getBackupDisk();

        if (! $disk->has($backup->path)) {
            abort(404);
        }

        $url = $disk->temporaryUrl($backup->path, now()->addMinutes(15));

        return redirect($url);
    }

    private function getBackupFromName($name)
    {
        $hash = str_replace('.zip', '', $name);
        $backups = $this->listBackups();

        if (! $backups->has($hash)) {
            abort(404);
        }

        return $backups->get($hash);
    }

    private function listBackups()
    {
        $cachekey = 'system:database:backups';

        $ttl = [
            now()->addMinutes(15),
            now()->addMinutes(60),
        ];

        return Cache::flexible($cachekey, $ttl, function () {
            try {
                $files = $this->getBackupDisk()
                    ->listContents(config('backup.backup.name'))
                    ->filter(fn(StorageAttributes $attributes) => $attributes->isFile())
                    ->sortByPath();
            } catch (UnableToListContents) {
                // backup disk not correctly configured
                return collect();
            } catch (CredentialsException|InvalidArgumentException) {
                // backup disk not configured
                return collect();
            }

            return collect($files)
                ->sortByDesc(fn($file) => $file->lastModified())
                ->map(function ($file) {
                    /** @var FileAttributes $file */
                    $path = $file->path();
                    $hash = hash('xxh3', $path);
                    $extension = pathinfo($path, PATHINFO_EXTENSION);

                    return (object) [
                        'hash' => $hash,
                        'path' => $path,
                        'filesize' => $file->fileSize(),
                        'url' => route('admin.system.database.download', [$hash . '.' . $extension]),
                        'basename' => basename($path),
                        'createdAt' => Date::createFromTimestamp($file->lastModified()),
                    ];
                })
                ->filter()
                ->values()
                ->slice(0, 14)
                ->keyBy('hash');
        });
    }

    private function listDatabaseVariables()
    {
        $list = [
            'character_set_client' => 'Character set (client)',
            'performance_schema' => 'Performance schema',
            'slow_query_log' => 'Slow query log',
            'sql_mode' => 'SQL mode',
            'version' => 'Version',
        ];

        $results = DB::connection('tenant')
            ->getPdo()
            ->query('SHOW VARIABLES')
            ->fetchAll();

        $results = collect($results)
            ->pluck('Value', 'Variable_name')
            ->filter(fn($item, $key) =>
                /** @var string $key */
                array_key_exists($key, $list))
            ->map(function ($item, $key) use ($list) {
                if ($key == 'sql_mode') {
                    $item = collect(explode(',', $item))->implode(PHP_EOL);
                }

                return (object) [
                    'title' => Arr::get($list, $key),
                    'value' => $item,
                ];
            });

        return $results;
    }

    private function getDatabaseStats()
    {
        $config = DB::connection('tenant')->getConfig();

        if ($config['driver'] !== 'mysql') {
            return [];
        }

        $result = $this->getDbConnection()
            ->table('information_schema.TABLES')
            ->where('TABLE_SCHEMA', '=', $config['database'])
            ->selectRaw('SUM(table_rows) as rows_quantity')
            ->selectRaw('SUM(data_length + index_length) as size')
            ->groupBy('TABLE_SCHEMA')
            ->first();

        return [
            'rows' => (int) $result->rows_quantity,
            'size' => (int) $result->size,
        ];
    }

    private function getBackupDisk(): FilesystemAdapter
    {
        return Disk::TENANT_BACKUP->storage();
    }

    private function getDbConnection()
    {
        static $connection;

        if (! isset($connection)) {
            $config = DB::getConfig();

            Arr::forget($config, 'prefix');
            config(['database.connections.global' => $config]);

            $connection = DB::connection('global');
        }

        return $connection;
    }
}
