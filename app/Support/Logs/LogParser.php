<?php

declare(strict_types=1);

namespace App\Support\Logs;

use Generator;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Date;
use InvalidArgumentException;
use JsonException;

class LogParser
{
    private const DEFAULT_PAGE_SIZE = 50;
    private const MAX_PAGE_SIZE = 1000;

    public function __construct(
        private readonly string $logPath,
        private readonly int $pageSize = self::DEFAULT_PAGE_SIZE
    ) {
        if (! file_exists($this->logPath)) {
            throw new InvalidArgumentException("Log file does not exist: {$this->logPath}");
        }

        if ($this->pageSize <= 0 || $this->pageSize > self::MAX_PAGE_SIZE) {
            throw new InvalidArgumentException('Page size must be between 1 and ' . self::MAX_PAGE_SIZE);
        }
    }

    public function getPage(int $page = 1): LengthAwarePaginator
    {
        if ($page < 1) {
            throw new InvalidArgumentException('Page number must be 1 or greater');
        }

        $offset = ($page - 1) * $this->pageSize;
        $entries = [];
        $currentIndex = 0;
        $collected = 0;

        // Only read the lines we need for this specific page
        foreach ($this->readLinesReverse() as $index => $line) {
            // Skip lines until we reach the offset for this page
            if ($currentIndex < $offset) {
                $currentIndex++;

                continue;
            }

            // Collect lines for this page
            if ($collected < $this->pageSize) {
                try {
                    $entries[] = $this->parseLogEntry($line, $index);
                    $collected++;
                } catch (JsonException) {
                    // Skip malformed JSON lines, don't count towards collected
                    continue;
                }
            } else {
                // We have enough entries for this page
                break;
            }

            $currentIndex++;
        }

        // Get total count efficiently (only when needed for pagination info)
        $totalEntries = $this->countTotalLines();

        return new LengthAwarePaginator(
            items: $entries,
            total: $totalEntries,
            perPage: $this->pageSize,
            currentPage: $page,
            options: [
                'path' => optional(request())->url() ?? '',
            ],
        );
    }

    /**
     * Find a log entry by its unique ID
     */
    public function findByUniqueId(string $uniqueId): ?LogEntry
    {
        foreach ($this->readLinesReverse() as $index => $line) {
            try {
                $entry = $this->parseLogEntry($line, $index);
                if ($entry->getUniqueId() === $uniqueId) {
                    return $entry;
                }
            } catch (JsonException) {
                continue;
            }
        }

        return null;
    }

    /**
     * Find a log entry by its content ID
     */
    public function findByContentId(string $contentId): ?LogEntry
    {
        foreach ($this->readLinesReverse() as $index => $line) {
            try {
                $entry = $this->parseLogEntry($line, $index);
                if ($entry->getContentId() === $contentId) {
                    return $entry;
                }
            } catch (JsonException) {
                continue;
            }
        }

        return null;
    }

    /**
     * Find a log entry by its index (backward compatibility)
     */
    public function findByIndex(int $index): ?LogEntry
    {
        foreach ($this->readLinesReverse() as $lineIndex => $line) {
            if ($lineIndex === $index) {
                try {
                    return $this->parseLogEntry($line, $lineIndex);
                } catch (JsonException) {
                    return null;
                }
            }
        }

        return null;
    }

    /**
     * Find a log entry by its signature ID
     */
    public function findBySignatureId(string $signatureId): ?LogEntry
    {
        foreach ($this->readLinesReverse() as $index => $line) {
            try {
                $entry = $this->parseLogEntry($line, $index);
                if ($entry->getSignatureId() === $signatureId) {
                    return $entry;
                }
            } catch (JsonException) {
                continue;
            }
        }

        return null;
    }

    public function getLatest(?int $limit = null): array
    {
        $limit = $limit ?? $this->pageSize;

        if ($limit <= 0 || $limit > self::MAX_PAGE_SIZE) {
            throw new InvalidArgumentException('Limit must be between 1 and ' . self::MAX_PAGE_SIZE);
        }

        $entries = [];
        $count = 0;

        // Read lines in reverse order for latest entries
        foreach ($this->readLinesReverse() as $index => $line) {
            if ($count >= $limit) {
                break;
            }

            try {
                $entries[] = $this->parseLogEntry($line, $index);
                $count++;
            } catch (JsonException) {
                // Skip malformed JSON lines
                continue;
            }
        }

        return $entries;
    }

    private function readLinesReverse(): Generator
    {
        $file = fopen($this->logPath, 'r');
        if (! $file) {
            throw new InvalidArgumentException("Cannot read log file: {$this->logPath}");
        }

        try {
            // Get file size
            fseek($file, 0, SEEK_END);
            $fileSize = ftell($file);

            if ($fileSize === 0) {
                return;
            }

            $buffer = '';
            $pos = $fileSize;
            $chunkSize = 8192;

            while ($pos > 0) {
                $readSize = min($chunkSize, $pos);
                $pos -= $readSize;
                fseek($file, $pos);
                $chunk = fread($file, $readSize);
                $buffer = $chunk . $buffer;

                // Process complete lines
                $lines = explode("\n", $buffer);
                $buffer = array_shift($lines); // Keep incomplete line in buffer

                // Yield lines in reverse order
                foreach (array_reverse($lines) as $line) {
                    if (trim($line) !== '') {
                        yield trim($line);
                    }
                }
            }

            // Yield the last line if any
            if (trim($buffer) !== '') {
                yield trim($buffer);
            }
        } finally {
            fclose($file);
        }
    }

    private function countTotalLines(): int
    {
        $file = new \SplFileObject($this->logPath);
        $file->seek(PHP_INT_MAX);

        return $file->key() + 1;
    }

    private function parseLogEntry(string $line, int $index): LogEntry
    {
        $data = json_decode($line, true, 512, JSON_THROW_ON_ERROR);

        return new LogEntry(
            index: $index,
            message: $data['message'] ?? '',
            level: $data['level'] ?? 0,
            levelName: $data['level_name'] ?? 'UNKNOWN',
            channel: $data['channel'] ?? 'unknown',
            datetime: isset($data['datetime']) ? Date::parse($data['datetime']) : null,
            context: $data['context'] ?? [],
            extra: $data['extra'] ?? [],
            raw: $line
        );
    }

}
