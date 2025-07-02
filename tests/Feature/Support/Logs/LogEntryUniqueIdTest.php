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

    // IDs should be different for different entries
    expect($entry1->getId())->not->toBe($entry2->getId());

    // IDs should be different for different content
    expect($entry1->getId())->not->toBe($entry2->getId());
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
    $request->setRouteResolver(fn() => $route);

    $array = LogEntryResource::make($entry)->resolve($request);

    // Test the array response properties
    expect($array)->toBeArray();
    expect($array['id'])->toBeString();
    expect($array['id'])->toBe('32d343efca13ba14');
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
});
