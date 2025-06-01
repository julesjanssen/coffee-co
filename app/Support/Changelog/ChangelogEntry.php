<?php

declare(strict_types=1);

namespace App\Support\Changelog;

readonly class ChangelogEntry
{
    public function __construct(
        public string $type,
        public string $description,
    ) {}
}
