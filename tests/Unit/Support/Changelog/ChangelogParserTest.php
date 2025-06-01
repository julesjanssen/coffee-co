<?php

declare(strict_types=1);

use App\Support\Changelog\ChangelogParser;
use App\Support\Changelog\ChangelogVersion;
use Carbon\Carbon;

beforeEach(function () {
    $this->testChangelogContent = <<<'MD'
# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased]

### Added
- New feature coming soon
- Another upcoming feature

### Fixed
- Bug fix in development

## [1.1.0] - 2023-12-01

### Added
- System tasks functionality
- JsExportable attribute

### Changed
- Updated build tooling
- Improved type implementation

### Fixed
- Login failure handling

## [1.0.0] - 2023-11-01

### Added
- Initial release
- Basic functionality

MD;

    $this->testChangelogPath = getcwd() . '/test-changelog.md';
    file_put_contents($this->testChangelogPath, $this->testChangelogContent);
});

afterEach(function () {
    if (file_exists($this->testChangelogPath)) {
        unlink($this->testChangelogPath);
    }
});

it('can parse changelog file', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $versions = $parser->parse();

    expect($versions)->toHaveCount(3);
    expect($versions->first()->isUnreleased)->toBeTrue();
    expect($versions->get(1)->version)->toBe('1.1.0');
    expect($versions->get(2)->version)->toBe('1.0.0');
});

it('can get latest version', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $latest = $parser->getLatestVersion();

    expect($latest)->toBeInstanceOf(ChangelogVersion::class);
    expect($latest->isUnreleased)->toBeTrue();
});

it('can get unreleased changes', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $unreleased = $parser->getUnreleasedChanges();

    expect($unreleased)->toBeInstanceOf(ChangelogVersion::class);
    expect($unreleased->version)->toBe('Unreleased');
    expect($unreleased->isUnreleased)->toBeTrue();
});

it('can get specific version', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $version = $parser->getVersion('1.1.0');

    expect($version)->toBeInstanceOf(ChangelogVersion::class);
    expect($version->version)->toBe('1.1.0');
    expect($version->date)->toEqual(Carbon::createFromFormat('Y-m-d', '2023-12-01'));
});

it('parses change entries correctly', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $version = $parser->getVersion('1.1.0');

    $addedEntries = $version->getEntriesOfType('Added');
    expect($addedEntries)->toHaveCount(2);
    expect($addedEntries[0]->description)->toBe('System tasks functionality');
    expect($addedEntries[1]->description)->toBe('JsExportable attribute');

    $changedEntries = $version->getEntriesOfType('Changed');
    expect($changedEntries)->toHaveCount(2);

    $fixedEntries = $version->getEntriesOfType('Fixed');
    expect($fixedEntries)->toHaveCount(1);
});

it('groups entries by type', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $version = $parser->getVersion('1.1.0');

    $entriesByType = $version->getEntriesByType();

    expect($entriesByType)->toHaveKeys(['Added', 'Changed', 'Fixed']);
    expect($entriesByType['Added'])->toHaveCount(2);
    expect($entriesByType['Changed'])->toHaveCount(2);
    expect($entriesByType['Fixed'])->toHaveCount(1);
});

it('throws exception when changelog file not found', function () {
    $parser = new ChangelogParser('non-existent-changelog.md');

    expect(fn() => $parser->parse())
        ->toThrow(InvalidArgumentException::class, 'Changelog file not found');
});

it('returns null for non-existent version', function () {
    $parser = new ChangelogParser('test-changelog.md');
    $version = $parser->getVersion('999.0.0');

    expect($version)->toBeNull();
});

it('handles invalid date formats gracefully', function () {
    $invalidDateContent = <<<'MD'
## [1.0.0] - invalid-date

### Added
- Something
MD;

    file_put_contents($this->testChangelogPath, $invalidDateContent);

    $parser = new ChangelogParser('test-changelog.md');
    $version = $parser->getVersion('1.0.0');

    expect($version->date)->toBeNull();
});
