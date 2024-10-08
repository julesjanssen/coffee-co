<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin\System;

use App\Models\Tenant;
use Aws\Exception\CredentialsException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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
            'backups' => $this->listBackups()->values(),
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
            now()->addMinutes(20),
        ];

        return Cache::flexible($cachekey, $ttl, function () {
            try {
                $disk = $this->getBackupDisk();
                if (empty($disk)) {
                    return collect();
                }

                $files = $disk->getDriver()->listContents(config('backup.backup.name'))
                    ->filter(function (StorageAttributes $attributes) {
                        return $attributes->isFile();
                    })
                    ->sortByPath();
            } catch (UnableToListContents $e) {
                // backup disk not correctly configured
                return collect();
            } catch (CredentialsException $e) {
                // backup disk not configured
                return collect();
            } catch (InvalidArgumentException $e) {
                // backup disk not configured
                return collect();
            }

            $tenant = Tenant::current();

            $files = collect($files)
                ->sortByDesc(fn($file) => $file->lastModified())
                ->map(function ($file) use ($tenant) {
                    /** @var FileAttributes $file */
                    $basename = basename($file->path());
                    if (! preg_match('/^\d{12}-\d{4}-/', $basename)) {
                        return;
                    }

                    $tenantID = (int) substr($basename, 13, 4);
                    if ($tenantID !== $tenant->id) {
                        return;
                    }

                    $filesize = $file->fileSize();
                    $date = Date::createFromTimestamp($file->lastModified());
                    $hash = hash('xxh3', $file->path());
                    $extension = pathinfo($basename, PATHINFO_EXTENSION);

                    return (object) [
                        'hash' => $hash,
                        'path' => $file->path(),
                        'filesize' => $filesize,
                        'url' => route('admin.system.database.download', [$hash . '.' . $extension]),
                        'basename' => $basename,
                        'createdAt' => $date,
                    ];
                })
                ->filter()
                ->values()
                ->slice(0, 14)
                ->keyBy('hash');

            return $files;
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

        $results = DB::getPdo()
            ->query('SHOW VARIABLES')
            ->fetchAll();

        $results = collect($results)
            ->pluck('Value', 'Variable_name')
            ->filter(function ($item, $key) use ($list) {
                /** @var string $key */
                return array_key_exists($key, $list);
            })
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
        $config = DB::getConfig();

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

    private function getBackupDisk(): ?FilesystemAdapter
    {
        $backupDisks = config('backup.backup.destination.disks', []);

        if (! count($backupDisks)) {
            return null;
        }

        return Storage::disk(head($backupDisks));
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
