<?php

declare(strict_types=1);

use App\Http\Resources\Admin\LogEntryResource;
use App\Support\Logs\LogEntry;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Date;

it('generates unique IDs for log entries', function () {
    $datetime = Date::parse('2025-01-06 12:00:00');

    $entry1 = new LogEntry(
        index: 1,
        message: 'Test message 1',
        level: 200,
        levelName: 'INFO',
        channel: 'local',
        datetime: $datetime,
        context: [],
        extra: [],
        raw: '{"message":"Test message 1","level":200}'
    );

    $entry2 = new LogEntry(
        index: 2,
        message: 'Test message 2',
        level: 200,
        levelName: 'INFO',
        channel: 'local',
        datetime: $datetime,
        context: [],
        extra: [],
        raw: '{"message":"Test message 2","level":200}'
    );

    // Unique IDs should be different for different entries
    expect($entry1->getUniqueId())->not->toBe($entry2->getUniqueId());

    // Content IDs should be different for different content
    expect($entry1->getContentId())->not->toBe($entry2->getContentId());

    // Same entry content should produce same content and signature IDs
    $entry1Copy = new LogEntry(
        index: 999, // Different index shouldn't affect content-based IDs
        message: 'Test message 1',
        level: 200,
        levelName: 'INFO',
        channel: 'local',
        datetime: $datetime,
        context: [],
        extra: [],
        raw: '{"message":"Test message 1","level":200}'
    );

    // Content ID should be the same for same raw content
    expect($entry1->getContentId())->toBe($entry1Copy->getContentId());

    // Signature ID should be the same for same entry properties
    expect($entry1->getSignatureId())->toBe($entry1Copy->getSignatureId());

    // Unique ID should be the same for same timestamp and content
    expect($entry1->getUniqueId())->toBe($entry1Copy->getUniqueId());
});

it('can convert log entry to array with unique identifiers', function () {
    $datetime = Date::parse('2025-01-06 12:10:20.789');

    $entry = new LogEntry(
        index: 5,
        message: 'Test message',
        level: 400,
        levelName: 'ERROR',
        channel: 'local',
        datetime: $datetime,
        context: ['user_id' => 123],
        extra: [],
        raw: '{"message":"Test message","level":400}'
    );

    $filename = 'laravel-2025-01-06.log';
    $route = route('admin.system.logs.view', ['filename' => $filename]);

    $request = Request::create($route, 'GET');
    $route = new Route(['GET'], str_replace($filename, '{filename}', $route), []);
    $route->bind($request);
    $route->setParameter('filename', $filename);
    $request->setRouteResolver(function () use ($route) {
        return $route;
    });

    $array = LogEntryResource::make($entry)->resolve($request);

    // Test the array response properties
    expect($array)->toBeArray();
    expect($array['index'])->toBe(5);
    expect($array['uniqueId'])->toBeString();
    expect($array['uniqueId'])->toBe('1736165420-789000-a62d2ae0d0101627');
    expect($array['contentId'])->toBeString();
    expect($array['contentId'])->toBe('a62d2ae0d0101627');
    expect($array['signatureId'])->toBeString();
    expect($array['signatureId'])->toBe('32d343efca13ba14');
    expect($array['message'])->toBe('Test message');
    expect($array['level'])->toBe(400);
    expect($array['levelName'])->toBe('ERROR');
    expect($array['channel'])->toBe('local');
    expect($array['datetime'])->toBe('2025-01-06T12:10:20.789000Z');
    expect($array['context'])->toBe(['user_id' => 123]);
    expect($array['extra'])->toBe([]);
    expect($array['hasException'])->toBeBool();
    expect($array['isError'])->toBeTrue();
    expect($array['isCritical'])->toBeBool();

    // Verify all three ID types are different
    expect($array['uniqueId'])->not->toBe($array['contentId']);
    expect($array['uniqueId'])->not->toBe($array['signatureId']);
    expect($array['contentId'])->not->toBe($array['signatureId']);
});
