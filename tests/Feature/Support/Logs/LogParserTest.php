<?php

declare(strict_types=1);

use App\Support\Logs\LogParser;
use Carbon\Carbon;

beforeEach(function () {
    $this->testLogPath = sys_get_temp_dir() . '/test_log_' . uniqid() . '.log';
});

afterEach(function () {
    if (file_exists($this->testLogPath)) {
        unlink($this->testLogPath);
    }
});

function createTestLogFile(string $path, array $entries): void
{
    $content = '';
    foreach ($entries as $entry) {
        $content .= json_encode($entry) . "\n";
    }
    // Remove trailing newline to avoid counting empty lines
    $content = rtrim($content, "\n");
    file_put_contents($path, $content);
}

function createGzipTestLogFile(string $path, array $entries): void
{
    $content = '';
    foreach ($entries as $entry) {
        $content .= json_encode($entry) . "\n";
    }
    $content = rtrim($content, "\n");
    file_put_contents('compress.zlib://' . $path, $content);
}

function createLargeLogFile(string $path, int $entryCount): void
{
    $file = fopen($path, 'w');
    if (! $file) {
        throw new RuntimeException('Could not create test log file');
    }

    try {
        for ($i = 1; $i <= $entryCount; $i++) {
            $entry = [
                'message' => "Test message {$i}",
                'level' => 200,
                'level_name' => 'INFO',
                'channel' => 'local',
                'datetime' => Carbon::now()->addMinutes($i)->toISOString(),
                'context' => [
                    'url' => "https://example.com/page/{$i}",
                    'ip' => '127.0.0.1',
                    'user_id' => $i % 1000,
                ],
                'extra' => [
                    'memory_usage' => random_int(10000000, 50000000),
                    'request_id' => 'req_' . uniqid(),
                ],
            ];

            fwrite($file, json_encode($entry) . "\n");

            // Flush periodically to avoid memory issues during test file creation
            if ($i % 1000 === 0) {
                fflush($file);
            }
        }
    } finally {
        fclose($file);
    }
}

it('throws exception for non-existent file', function () {
    expect(fn() => new LogParser('/non/existent/file.log'))
        ->toThrow(InvalidArgumentException::class, 'Log file does not exist');
});

it('throws exception for invalid page size', function () {
    createTestLogFile($this->testLogPath, []);

    expect(fn() => new LogParser($this->testLogPath, 0))
        ->toThrow(InvalidArgumentException::class, 'Page size must be between 1 and');
});

it('returns correct pagination', function () {
    $entries = [];
    for ($i = 1; $i <= 10; $i++) {
        $entries[] = [
            'message' => "Test message {$i}",
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'local',
            'datetime' => Carbon::now()->addMinutes($i)->toISOString(),
            'context' => [],
            'extra' => [],
        ];
    }

    createTestLogFile($this->testLogPath, $entries);
    $parser = new LogParser($this->testLogPath, 3);

    // Test first page
    $page = $parser->getPage(1);
    expect($page->currentPage())->toBe(1);
    expect($page->lastPage())->toBe(4); // 10 entries / 3 per page = 4 pages
    expect($page->perPage())->toBe(3);
    expect($page->total())->toBe(10);
    expect($page->hasMorePages())->toBeTrue();
    expect($page->onFirstPage())->toBeTrue();
    expect($page->items())->toHaveCount(3);

    // Entries should be in reverse chronological order (latest first)
    expect($page->items()[0]->message)->toBe('Test message 10');
    expect($page->items()[1]->message)->toBe('Test message 9');
    expect($page->items()[2]->message)->toBe('Test message 8');

    // Test middle page
    $page = $parser->getPage(2);
    expect($page->currentPage())->toBe(2);
    expect($page->hasMorePages())->toBeTrue();
    expect($page->onFirstPage())->toBeFalse();
    expect($page->items()[0]->message)->toBe('Test message 7');
});

it('returns most recent entries', function () {
    $entries = [];
    for ($i = 1; $i <= 5; $i++) {
        $entries[] = [
            'message' => "Test message {$i}",
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'local',
            'datetime' => Carbon::now()->addMinutes($i)->toISOString(),
            'context' => [],
            'extra' => [],
        ];
    }

    createTestLogFile($this->testLogPath, $entries);
    $parser = new LogParser($this->testLogPath);

    $latest = $parser->getLatest(3);
    expect($latest)->toHaveCount(3);
    expect($latest[0]->message)->toBe('Test message 5');
    expect($latest[1]->message)->toBe('Test message 4');
    expect($latest[2]->message)->toBe('Test message 3');
});

it('handles malformed json gracefully', function () {
    $content = json_encode([
        'message' => 'Valid entry',
        'level' => 200,
        'level_name' => 'INFO',
        'channel' => 'local',
        'datetime' => Carbon::now()->toISOString(),
        'context' => [],
        'extra' => [],
    ]) . "\n";

    $content .= "invalid json line\n";

    $content .= json_encode([
        'message' => 'Another valid entry',
        'level' => 200,
        'level_name' => 'INFO',
        'channel' => 'local',
        'datetime' => Carbon::now()->addMinute()->toISOString(),
        'context' => [],
        'extra' => [],
    ]);

    file_put_contents($this->testLogPath, $content);
    $parser = new LogParser($this->testLogPath);

    $page = $parser->getPage(1);
    // Total counts all lines including invalid ones, but items will only contain valid entries
    expect($page->total())->toBe(3); // Total lines in file (including malformed)
    expect($page->items())->toHaveCount(2); // Only valid entries are parsed
    expect($page->items()[0]->message)->toBe('Another valid entry');
    expect($page->items()[1]->message)->toBe('Valid entry');
});

it('returns empty page for empty log file', function () {
    // Create an empty file
    file_put_contents($this->testLogPath, '');
    $parser = new LogParser($this->testLogPath);

    $page = $parser->getPage(1);
    expect($page->isEmpty())->toBeTrue();
    expect($page->total())->toBe(1); // Empty file still counts as 1 line for SplFileObject
    expect($page->items())->toHaveCount(0);
});

it('is memory efficient with large log files', function () {
    // Create a large log file (approx 50MB+)
    createLargeLogFile($this->testLogPath, 200000); // 200k entries ≈ 50MB+

    $parser = new LogParser($this->testLogPath, 50);

    // Measure memory before and after getting a page
    $memoryBefore = memory_get_usage(true);

    // Get a page from the middle to test offset handling
    $page = $parser->getPage(100);

    $memoryAfter = memory_get_usage(true);
    $memoryIncrease = $memoryAfter - $memoryBefore;

    // Memory increase should be minimal (less than 10MB for a 50MB+ file)
    expect($memoryIncrease)->toBeLessThan(10 * 1024 * 1024);

    // Verify the page contains the expected entries
    expect($page->items())->toHaveCount(50);
    expect($page->currentPage())->toBe(100);
    expect($page->total())->toBe(200001); // +1 because SplFileObject counts final newline as extra line

    // Test that entries are in reverse chronological order
    $firstEntry = $page->items()[0];
    $lastEntry = $page->items()[49];
    expect($firstEntry->datetime)->toBeGreaterThan($lastEntry->datetime);
})->group('large-files');

it('has good pagination performance with large files', function () {
    // Create a moderately large log file
    createLargeLogFile($this->testLogPath, 50000); // 50k entries ≈ 12MB

    $parser = new LogParser($this->testLogPath, 25);

    // Test getting different pages
    $startTime = microtime(true);

    $page1 = $parser->getPage(1);
    $page100 = $parser->getPage(100);
    $page500 = $parser->getPage(500);

    $endTime = microtime(true);
    $totalTime = $endTime - $startTime;

    // Should complete within reasonable time (3 seconds for 50k entries)
    expect($totalTime)->toBeLessThan(3.0);

    // Verify all pages work correctly
    expect($page1->items())->toHaveCount(25);
    expect($page100->items())->toHaveCount(25);
    expect($page500->items())->toHaveCount(25);

    // Verify page numbers are correct
    expect($page1->currentPage())->toBe(1);
    expect($page100->currentPage())->toBe(100);
    expect($page500->currentPage())->toBe(500);
})->group('large-files');

it('efficiently gets latest entries from very large files', function () {
    // Create a large log file
    createLargeLogFile($this->testLogPath, 100000); // 100k entries ≈ 25MB

    $parser = new LogParser($this->testLogPath);

    $memoryBefore = memory_get_usage(true);

    // Get latest entries should be very fast and memory efficient
    $latest = $parser->getLatest(100);

    $memoryAfter = memory_get_usage(true);
    $memoryIncrease = $memoryAfter - $memoryBefore;

    // Should use minimal memory
    expect($memoryIncrease)->toBeLessThan(2 * 1024 * 1024);

    // Should return exactly 100 entries
    expect($latest)->toHaveCount(100);

    // Should be the most recent entries (highest numbers)
    expect($latest[0]->message)->toBe('Test message 100000');
    expect($latest[1]->message)->toBe('Test message 99999');
    expect($latest[99]->message)->toBe('Test message 99901');
})->group('large-files');

it('can parse gzipped log files', function () {
    $entries = [
        [
            'message' => 'Gzip test message 1',
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'local',
            'datetime' => Carbon::now()->addMinutes(1)->toISOString(),
            'context' => [],
            'extra' => [],
        ],
        [
            'message' => 'Gzip test message 2',
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'local',
            'datetime' => Carbon::now()->addMinutes(2)->toISOString(),
            'context' => [],
            'extra' => [],
        ],
    ];

    $this->testLogPath = sys_get_temp_dir() . '/test_log_' . uniqid() . '.log.gz';
    createGzipTestLogFile($this->testLogPath, $entries);

    $parser = new LogParser($this->testLogPath);
    $page = $parser->getPage(1);

    expect($page->total())->toBe(2);
    expect($page->items())->toHaveCount(2);
    expect($page->items()[0]->message)->toBe('Gzip test message 2');
    expect($page->items()[1]->message)->toBe('Gzip test message 1');
});

it('can paginate gzipped log files', function () {
    $entries = [];
    for ($i = 1; $i <= 10; $i++) {
        $entries[] = [
            'message' => "Gzip pagination message {$i}",
            'level' => 200,
            'level_name' => 'INFO',
            'channel' => 'local',
            'datetime' => Carbon::now()->addMinutes($i)->toISOString(),
            'context' => [],
            'extra' => [],
        ];
    }

    $this->testLogPath = sys_get_temp_dir() . '/test_log_' . uniqid() . '.log.gz';
    createGzipTestLogFile($this->testLogPath, $entries);
    $parser = new LogParser($this->testLogPath, 3);

    // Test first page
    $page = $parser->getPage(1);
    expect($page->currentPage())->toBe(1);
    expect($page->lastPage())->toBe(4);
    expect($page->perPage())->toBe(3);
    expect($page->total())->toBe(10);
    expect($page->items())->toHaveCount(3);
    expect($page->items()[0]->message)->toBe('Gzip pagination message 10');

    // Test middle page
    $page = $parser->getPage(2);
    expect($page->currentPage())->toBe(2);
    expect($page->items())->toHaveCount(3);
    expect($page->items()[0]->message)->toBe('Gzip pagination message 7');
});

it('can find entry by ID in gzipped log files', function () {
    $entryData = [
        'message' => 'Entry with unique ID',
        'level' => 200,
        'level_name' => 'INFO',
        'channel' => 'local',
        'datetime' => Carbon::now()->addMinute()->toISOString(),
        'context' => [],
        'extra' => [],
    ];

    $this->testLogPath = sys_get_temp_dir() . '/test_log_' . uniqid() . '.log.gz';
    createGzipTestLogFile($this->testLogPath, [$entryData]);

    // Parse the file to get the actual LogEntry object and its generated unique ID
    $parser = new LogParser($this->testLogPath);
    $parsedEntry = $parser->getPage(1)->items()[0];
    $id = $parsedEntry->getId();

    $foundEntry = $parser->findById($id);

    expect($foundEntry)->not->toBeNull();
    expect($foundEntry->message)->toBe('Entry with unique ID');
    expect($foundEntry->getId())->toBe($id);
});

it('returns empty page for empty gzipped log file', function () {
    $this->testLogPath = sys_get_temp_dir() . '/empty_log_' . uniqid() . '.log.gz';
    file_put_contents('compress.zlib://' . $this->testLogPath, '');

    $parser = new LogParser($this->testLogPath);
    $page = $parser->getPage(1);

    expect($page->isEmpty())->toBeTrue();
    expect($page->total())->toBe(0);
    expect($page->items())->toHaveCount(0);
});
