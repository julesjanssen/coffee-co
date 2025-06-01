<?php

declare(strict_types=1);

use App\Support\Logs\LogEntry;
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
    $datetime = Date::parse('2025-01-06 12:00:00');

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

    $array = $entry->toArray();

    expect($array)->toHaveKeys([
        'index', 'uniqueId', 'contentId', 'signatureId', 'message', 'level', 'levelName',
        'channel', 'datetime', 'context', 'extra', 'hasException',
        'isError', 'isCritical', 'userId', 'tenantId', 'url', 'ip',
    ]);

    expect($array['index'])->toBe(5);
    expect($array['uniqueId'])->toBeString();
    expect($array['contentId'])->toBeString();
    expect($array['signatureId'])->toBeString();
    expect($array['message'])->toBe('Test message');
    expect($array['isError'])->toBeTrue();

    // Verify all three ID types are different
    expect($array['uniqueId'])->not->toBe($array['contentId']);
    expect($array['uniqueId'])->not->toBe($array['signatureId']);
    expect($array['contentId'])->not->toBe($array['signatureId']);
});
