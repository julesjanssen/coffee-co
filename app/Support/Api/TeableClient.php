<?php

declare(strict_types=1);

namespace App\Support\Api;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
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

        $tables = collect($response->json('tables', []));

        return $tables
            ->whereIn('name', $tableNames)
            ->mapWithKeys(fn(array $table) => [$table['name'] => $table['id']])
            ->collect();
    }

    public function getAllRecords(string $baseId, string $tableId): Collection
    {
        if (empty($baseId)) {
            throw new InvalidArgumentException('Base ID is required');
        }

        if (empty($tableId)) {
            throw new InvalidArgumentException('Table ID is required');
        }

        $allRecords = collect();
        $offset = null;

        do {
            $response = $this->getRecordsPage($baseId, $tableId, $offset);
            $data = $response->json();

            $records = collect($data['records'] ?? []);
            $allRecords = $allRecords->merge($records);

            $offset = $data['nextCursor'] ?? null;
        } while ($offset !== null);

        return $allRecords;
    }

    private function getRecordsPage(string $baseId, string $tableId, ?string $offset = null): Response
    {
        $query = [];
        if ($offset) {
            $query['offset'] = $offset;
        }

        return $this->client()
            ->get("/base/{$baseId}/table/{$tableId}/record", $query)
            ->throw();
    }
}
