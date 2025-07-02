<?php

declare(strict_types=1);

use App\Support\Logs\LogFilenameValidator;

it('returns true for valid .log filename', function () {
    expect(LogFilenameValidator::isValidLogFilename('laravel.log'))->toBeTrue();
    expect(LogFilenameValidator::isValidLogFilename('2023-01-01.log'))->toBeTrue();
    expect(LogFilenameValidator::isValidLogFilename('my-app-2023-01-01.log'))->toBeTrue();
});

it('returns true for valid .gz filename with .log', function () {
    expect(LogFilenameValidator::isValidLogFilename('laravel.log.gz'))->toBeTrue();
    expect(LogFilenameValidator::isValidLogFilename('2023-01-01.log.gz'))->toBeTrue();
    expect(LogFilenameValidator::isValidLogFilename('my-app-2023-01-01.log.gz'))->toBeTrue();
    expect(LogFilenameValidator::isValidLogFilename('my-app.log.2023-01-01.gz'))->toBeTrue();
});

it('returns false for invalid filenames', function () {
    expect(LogFilenameValidator::isValidLogFilename('image.png'))->toBeFalse();
    expect(LogFilenameValidator::isValidLogFilename('document.pdf'))->toBeFalse();
    expect(LogFilenameValidator::isValidLogFilename('archive.zip'))->toBeFalse();
    expect(LogFilenameValidator::isValidLogFilename('mylog.txt'))->toBeFalse();
    expect(LogFilenameValidator::isValidLogFilename('mylog.gz'))->toBeFalse(); // Missing .log
    expect(LogFilenameValidator::isValidLogFilename('log'))->toBeFalse();
    expect(LogFilenameValidator::isValidLogFilename(''))->toBeFalse();
});
