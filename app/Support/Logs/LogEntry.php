<?php

declare(strict_types=1);

namespace App\Support\Logs;

use Carbon\CarbonInterface;

class LogEntry
{
    public function __construct(
        public readonly int $index,
        public readonly string $message,
        public readonly int $level,
        public readonly string $levelName,
        public readonly string $channel,
        public readonly ?CarbonInterface $datetime,
        public readonly array $context,
        public readonly array $extra,
        public readonly string $raw
    ) {}

    public function hasException(): bool
    {
        return isset($this->context['exception']);
    }

    public function getException(): ?array
    {
        return $this->context['exception'] ?? null;
    }

    public function getStackTrace(): ?array
    {
        $exception = $this->getException();

        return $exception['trace'] ?? null;
    }

    public function isError(): bool
    {
        return $this->level >= 400; // ERROR level and above
    }

    public function isCritical(): bool
    {
        return $this->level >= 500; // CRITICAL level and above
    }

    public function getUserId(): ?int
    {
        return $this->context['userId'] ?? $this->extra['userId'] ?? null;
    }

    public function getTenantId(): ?int
    {
        return $this->extra['tenantId'] ?? $this->context['tenantId'] ?? null;
    }

    public function getUrl(): ?string
    {
        return $this->context['url'] ?? null;
    }

    public function getIp(): ?string
    {
        return $this->context['ip'] ?? null;
    }

    /**
     * Generate a unique identifier for this log entry based on its content and timestamp
     */
    public function getUniqueId(): string
    {
        // Use a combination of timestamp, content hash, and microseconds for uniqueness
        $contentHash = hash('xxh3', $this->raw);
        $timestamp = $this->datetime?->getTimestamp() ?? 0;
        $microseconds = $this->datetime?->format('u') ?? '000000';

        // Include more of the content hash since we're not using index
        return sprintf('%d-%s-%s', $timestamp, $microseconds, substr($contentHash, 0, 16));
    }

    /**
     * Generate a stable identifier based on line content (useful for referencing across file changes)
     */
    public function getContentId(): string
    {
        return hash('xxh3', $this->raw);
    }

    /**
     * Generate a signature-based identifier using multiple log entry properties
     * This is more collision-resistant than content-only hashing
     */
    public function getSignatureId(): string
    {
        $signature = sprintf(
            '%s|%s|%d|%s|%s',
            $this->datetime?->toISOString() ?? '',
            $this->message,
            $this->level,
            $this->channel,
            json_encode($this->context)
        );

        return hash('xxh3', $signature);
    }
}
