<?php

declare(strict_types=1);

namespace App\Support\Changelog;

use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;

class ChangelogParser
{
    private const array CHANGE_TYPES = ['Added', 'Changed', 'Deprecated', 'Removed', 'Fixed', 'Security'];

    public function __construct(
        private readonly string $changelogPath = 'CHANGELOG.md'
    ) {}

    public function parse(): Collection
    {
        $content = $this->getChangelogContent();
        $lines = explode("\n", $content);

        return $this->parseLines($lines);
    }

    public function getLatestVersion(): ?ChangelogVersion
    {
        return $this->parse()->first();
    }

    public function getUnreleasedChanges(): ?ChangelogVersion
    {
        return $this->parse()->first(fn($version) => $version->isUnreleased);
    }

    public function getVersion(string $versionNumber): ?ChangelogVersion
    {
        return $this->parse()->first(fn($version) => $version->version === $versionNumber);
    }

    private function getChangelogContent(): string
    {
        $path = $this->changelogPath;

        // If path is relative, make it relative to Laravel base path
        if (! str_starts_with($path, '/')) {
            $basePath = app()->bound('path.base') ? app('path.base') : getcwd();
            $path = $basePath . DIRECTORY_SEPARATOR . $path;
        }

        if (! file_exists($path)) {
            throw new InvalidArgumentException("Changelog file not found at: {$path}");
        }

        return file_get_contents($path);
    }

    /**
     * @param array<string> $lines
     */
    private function parseLines(array $lines): Collection
    {
        $versions = new Collection();
        $currentVersion = null;
        $currentType = null;
        $entries = [];

        foreach ($lines as $line) {
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            // Version header (## [1.0.0] - 2023-01-01 or ## [Unreleased])
            if (preg_match('/^## \[(.*?)\](?:\s*-\s*(.*))?$/', $line, $matches)) {
                // Save previous version if exists
                if ($currentVersion !== null) {
                    $versions->push(new ChangelogVersion(
                        $currentVersion['version'],
                        $currentVersion['date'],
                        $entries,
                        $currentVersion['isUnreleased']
                    ));
                }

                $version = $matches[1];
                $dateString = $matches[2] ?? null;
                $isUnreleased = strtolower($version) === 'unreleased';

                $date = null;
                if ($dateString && ! $isUnreleased) {
                    try {
                        $date = Date::createFromFormat('Y-m-d', trim($dateString));
                    } catch (Exception) {
                        // Invalid date format, keep as null
                    }
                }

                $currentVersion = [
                    'version' => $version,
                    'date' => $date,
                    'isUnreleased' => $isUnreleased,
                ];
                $currentType = null;
                $entries = [];

                continue;
            }

            // Change type header (### Added, ### Changed, etc.)
            if (preg_match('/^### (.+)$/', $line, $matches)) {
                $type = trim($matches[1]);
                if (in_array($type, self::CHANGE_TYPES)) {
                    $currentType = $type;
                }

                continue;
            }

            // Change entry (- Description)
            if ($currentType && preg_match('/^- (.+)$/', $line, $matches)) {
                $description = trim($matches[1]);
                $entries[] = new ChangelogEntry($currentType, $description);

                continue;
            }
        }

        // Save the last version
        if ($currentVersion !== null) {
            $versions->push(new ChangelogVersion(
                $currentVersion['version'],
                $currentVersion['date'],
                $entries,
                $currentVersion['isUnreleased']
            ));
        }

        return $versions;
    }
}
