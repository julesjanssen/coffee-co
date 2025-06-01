<?php

declare(strict_types=1);

namespace App\Support\Logs;

class LogPage
{
    public function __construct(
        public readonly array $entries,
        public readonly int $currentPage,
        public readonly int $totalPages,
        public readonly int $pageSize,
        public readonly int $totalEntries,
        public readonly bool $hasNextPage,
        public readonly bool $hasPreviousPage
    ) {}

    public function isEmpty(): bool
    {
        return empty($this->entries);
    }

    public function getEntryCount(): int
    {
        return count($this->entries);
    }

    public function getStartIndex(): int
    {
        return ($this->currentPage - 1) * $this->pageSize + 1;
    }

    public function getEndIndex(): int
    {
        return min($this->getStartIndex() + $this->getEntryCount() - 1, $this->totalEntries);
    }

    public function getNextPage(): ?int
    {
        return $this->hasNextPage ? $this->currentPage + 1 : null;
    }

    public function getPreviousPage(): ?int
    {
        return $this->hasPreviousPage ? $this->currentPage - 1 : null;
    }
}
