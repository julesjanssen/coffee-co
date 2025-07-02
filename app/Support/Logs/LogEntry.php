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
     * Generate a signature-based identifier using multiple log entry properties
     * This is more collision-resistant than content-only hashing
     */
    public function getId(): string
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
