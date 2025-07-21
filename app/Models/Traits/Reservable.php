<?php

declare(strict_types=1);

namespace App\Models\Traits;

use Carbon\CarbonImmutable;
use Carbon\CarbonInterface;
use Illuminate\Contracts\Cache\Lock;
use Illuminate\Support\Facades\Cache;
use UnitEnum;

trait Reservable
{
    public function reserve(mixed $key, string|int|CarbonInterface $duration = 60): bool
    {
        return $this->reservation($key, $duration)->get();
    }

    public function release(mixed $key): void
    {
        $this->reservation($key)->forceRelease();
    }

    public function reservation(mixed $key, string|int|CarbonInterface $duration = 60): Lock
    {
        // Convert e.g. +6 hours to a Carbon instance
        if (is_string($duration)) {
            $duration = CarbonImmutable::make($duration);
        }

        // Convert Carbon to seconds from now
        if ($duration instanceof CarbonInterface) {
            $duration = max(0, $duration->diffInSeconds(now()));
        }

        // Convert enums to strings
        if ($key instanceof UnitEnum) {
            $key = $key->name;
        }

        // Convert objects to strings
        if (is_object($key)) {
            $key = $key::class;
        }

        // Use the most stable methods of representing a model.
        return Cache::lock(
            "{$this->getMorphClass()}:{$this->getKey()}:{$key}",
            $duration
        );
    }
}
