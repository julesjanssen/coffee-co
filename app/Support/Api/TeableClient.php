<?php

declare(strict_types=1);

namespace App\Support\Api;

use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\LazyCollection;
use InvalidArgumentException;

class TeableClient
{
    public function __construct(
        private readonly string $apiEndpoint,
        private readonly string $apiToken,
    ) {
        if (empty($this->apiEndpoint)) {
            throw new InvalidArgumentException('Teable API endpoint is required');
        }

        if (empty($this->apiToken)) {
            throw new InvalidArgumentException('Teable API token is required');
        }
    }

    public static function make(): self
    {
        return new self(
            apiEndpoint: config('services.teable.api_endpoint'),
            apiToken: config('services.teable.api_token'),
        );
    }

    private function client(): PendingRequest
    {
        return Http::withToken($this->apiToken)
            ->baseUrl($this->apiEndpoint)
            ->timeout(30)
            ->retry(3, 100);
    }

    public function getTableIds(string $baseId, array $tableNames): Collection
    {
        if (empty($baseId)) {
            throw new InvalidArgumentException('Base ID is required');
        }

        if (empty($tableNames)) {
            throw new InvalidArgumentException('Table names array cannot be empty');
        }

        $response = $this->client()
            ->get("/base/{$baseId}/table")
            ->throw();

        $tables = collect($response->json());

        return $tables
            ->whereIn('name', $tableNames)
            ->mapWithKeys(fn(array $table) => [$table['name'] => $table['id']])
            ->collect();
    }

    public function getAllRecords(string $tableId): LazyCollection
    {
        if (empty($tableId)) {
            throw new InvalidArgumentException('Table ID is required');
        }

        return LazyCollection::make(function () use ($tableId): Generator {
            yield from $this->recordsGenerator($tableId);
        });
    }

    public function getAllRecordsEager(string $tableId): Collection
    {
        return $this->getAllRecords($tableId)->collect();
    }

    private function recordsGenerator(string $tableId): Generator
    {
        $skip = 0;
        $take = 100;

        do {
            $response = $this->getRecordsPage($tableId, $skip, $take);
            $data = $response->json();

            $records = $data['records'] ?? [];

            foreach ($records as $record) {
                yield $record;
            }

            $skip += $take;
        } while (count($records) === $take);
    }

    private function getRecordsPage(string $tableId, int $skip = 0, int $take = 100): Response
    {
        $query = [
            'take' => $take,
            'skip' => $skip,
        ];

        return $this->client()
            ->get("/table/{$tableId}/record", $query)
            ->throw();
    }
}
