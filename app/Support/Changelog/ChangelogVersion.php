<?php

declare(strict_types=1);

namespace App\Support\Changelog;

use Carbon\CarbonInterface;

readonly class ChangelogVersion
{
    /**
     * @param array<ChangelogEntry> $entries
     */
    public function __construct(
        public string $version,
        public ?CarbonInterface $date,
        public array $entries,
        public bool $isUnreleased = false,
    ) {}

    /**
     * @return array<string, array<ChangelogEntry>>
     */
    public function getEntriesByType(): array
    {
        $grouped = [];
        foreach ($this->entries as $entry) {
            $grouped[$entry->type][] = $entry;
        }

        return $grouped;
    }

    /**
     * @param string $type
     * @return array<ChangelogEntry>
     */
    public function getEntriesOfType(string $type): array
    {
        return array_filter($this->entries, fn($entry) => $entry->type === $type);
    }
}
