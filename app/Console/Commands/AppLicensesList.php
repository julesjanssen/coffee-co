<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Fluent;

class AppLicensesList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:licenses:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export a list of all licences of dependencies';

    protected $packages;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $packages = collect()
            ->merge($this->listJsPackages())
            ->merge($this->listPhpPackages())
            ->flatten(1)
            ->filter()
            ->sortBy('name')
            ->groupBy('license')
            ->map(fn($packages, $license) => $this->groupByAuthor($packages));

        $path = storage_path('app/open-source.json');
        file_put_contents($path, json_encode($packages, JSON_PRETTY_PRINT));
    }

    private function groupByAuthor($packages)
    {
        return $packages
            ->groupBy(function ($package) {
                $authorStr = collect($package->authors)
                    ->sort()
                    ->unique()
                    ->join(', ', ' and ');

                $package->authors = $authorStr;

                return hash('xxh3', (string) $authorStr);
            })
            ->map(fn($packages) => $packages->unique('name')->values())
            ->values();
    }

    protected function listJsPackages()
    {
        $result = Process::run('yarn licenses list --json');
        if (! $result->successful()) {
            return collect([]);
        }

        $line = collect(explode(PHP_EOL, $result->output()))
            ->map(function ($line) {
                if (empty($line)) {
                    return;
                }

                return json_decode($line, true);
            })
            ->first(fn($v) => $v['type'] === 'table');

        return collect($line['data']['body'])
            ->map(function ($item) {
                $url = $item[4];
                $url = filter_var($url, FILTER_VALIDATE_URL) ? $url : null;

                return new Fluent([
                    'name' => $item[0],
                    'url' => $url,
                    'authors' => [
                        $this->processAuthor($item[5]),
                    ],
                    'license' => $item[2],
                ]);
            });
    }

    private function processAuthor(string $author)
    {
        $author = trim($author);
        if (strtolower($author) === 'unknown') {
            return;
        }

        return $author;
    }

    protected function listPhpPackages()
    {
        $data = $this->readJson(base_path('composer.json'));

        return collect()
            ->merge(Arr::get($data, 'require', []))
            ->merge(Arr::get($data, 'require-dev', []))
            ->filter(fn($version, $name) => str_contains((string) $name, '/'))
            ->keys()
            ->map(function ($name) {
                $path = base_path('vendor/' . $name . '/composer.json');

                return $this->processConfigFile($path, $name);
            });

    }

    private function processConfigFile($path, $name)
    {
        $config = $this->readJson($path);

        $licenses = $this->findLicenseNames($config);
        $url = $this->findUrl($config);
        $authors = $this->findAuthors($config);

        if (! $licenses->count()) {
            return;
        }

        return new Fluent([
            'name' => $name,
            'url' => $url,
            'authors' => $authors,
            'license' => $licenses->first(),
        ]);
    }

    private function findAuthors($config)
    {
        $authors = Arr::get($config, 'authors', []);

        if (empty($authors)) {
            $author = Arr::get($config, 'author');
            if (! empty($author)) {
                $authors = [$author];
            }
        }

        return collect($authors)
            ->map(function ($author) {
                if (is_array($author)) {
                    $name = Arr::get($author, 'name');
                    $author = trim($name);
                }

                return $author;
            });
    }

    private function findUrl($config)
    {
        $url = Arr::get($config, 'homepage');
        if (empty($url)) {
            $url = Arr::get($config, 'repository');
        }

        if (is_array($url)) {
            $url = Arr::get($url, 'url');
        }

        return $url;
    }

    private function findLicenseNames($config)
    {
        $values = [];

        $value = Arr::get($config, 'license');
        if (! empty($value)) {
            if (is_array($value)) {
                $values = $value;
            } else {
                $values = [$value];
            }
        }

        if (empty($values)) {
            $values = Arr::get($config, 'licenses', []);
            $values = collect($values)
                ->map(fn($value) => Arr::get($value, 'type'))
                ->filter()
                ->toArray();
        }

        return collect($values)
            ->sortBy(function ($name) {
                if (strtoupper($name) === 'MIT') {
                    return -1;
                }

                return $name;
            })
            ->values();
    }

    private function readJson($path)
    {
        if (! file_exists($path)) {
            return [];
        }

        $json = file_get_contents($path);

        return json_decode($json, true);
    }
}
