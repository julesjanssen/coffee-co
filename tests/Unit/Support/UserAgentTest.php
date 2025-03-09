<?php

declare(strict_types=1);

use App\Support\Login\UserAgent;
use App\Support\Login\UserAgentDetails;
use DeviceDetector\Parser\Device\AbstractDeviceParser;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

uses(TestCase::class);

beforeEach(function () {
    // Clear cache to ensure fresh parsing
    Cache::flush();
});

it('parses Chrome browser user agent strings', function () {
    $userAgentString = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36';
    $details = UserAgent::getDetails($userAgentString);

    expect($details)->toBeInstanceOf(UserAgentDetails::class);
    expect($details->value)->toBe($userAgentString);
    expect($details->isBot)->toBeFalse();
    expect($details->clientFamily)->toBe('Chrome');
    expect($details->clientVersion)->toStartWith('91');
    expect($details->osName)->toBe('Windows');
    expect($details->osVersion)->toBe('10');
    expect($details->deviceName)->toBe('desktop');
    expect($details->deviceTypeIcon)->toBe('desktop');
});

it('parses Firefox browser user agent strings', function () {
    $userAgentString = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:89.0) Gecko/20100101 Firefox/89.0';
    $details = UserAgent::getDetails($userAgentString);

    expect($details->clientFamily)->toBe('Firefox');
    expect($details->clientVersion)->toStartWith('89');
    expect($details->osName)->toBe('Windows');
});

it('parses Safari browser user agent strings', function () {
    $userAgentString = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/15.0 Safari/605.1.15';
    $details = UserAgent::getDetails($userAgentString);

    expect($details->clientFamily)->toBe('Safari');
    expect($details->osName)->toBe('Mac');
});

it('parses iPhone user agent strings', function () {
    $userAgentString = 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1';
    $details = UserAgent::getDetails($userAgentString);

    expect($details->clientFamily)->toBe('Safari');
    expect($details->osName)->toBe('iOS');
    expect($details->deviceType)->toBe(AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE);
    expect($details->deviceName)->toBe('smartphone');
    expect($details->deviceTypeIcon)->toBe('phone');
});

it('parses iPad user agent strings', function () {
    $userAgentString = 'Mozilla/5.0 (iPad; CPU OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1';
    $details = UserAgent::getDetails($userAgentString);

    expect($details->osName)->toBe('iPadOS');
    expect($details->deviceType)->toBe(AbstractDeviceParser::DEVICE_TYPE_TABLET);
    expect($details->deviceTypeIcon)->toBe('tablet');
});

it('parses Android user agent strings', function () {
    $userAgentString = 'Mozilla/5.0 (Linux; Android 11; SM-G998B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.120 Mobile Safari/537.36';
    $details = UserAgent::getDetails($userAgentString);

    expect($details->clientFamily)->toBe('Chrome');
    expect($details->osName)->toBe('Android');
    expect($details->osVersion)->toBe('11');
    expect($details->deviceType)->toBe(AbstractDeviceParser::DEVICE_TYPE_SMARTPHONE);
});

it('identifies bot user agents', function () {
    $userAgentString = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)';
    $details = UserAgent::getDetails($userAgentString);

    expect($details->isBot)->toBeTrue();
    expect($details->deviceTypeIcon)->toBe('bot');
});

it('handles empty user agent strings', function () {
    $details = UserAgent::getDetails('');

    expect($details)->toBeInstanceOf(UserAgentDetails::class);
    expect($details->value)->toBe('');
    expect($details->deviceTypeIcon)->toBe('desktop');
});

it('sets default values for missing attributes', function () {
    // Create a UserAgentDetails with minimal data
    $details = new UserAgentDetails([
        'value' => 'test',
        'clientFamily' => 'Test Browser',
    ]);

    // Check default values are set
    expect($details->deviceTypeIcon)->toBe('desktop');
    expect($details->isBot)->toBeFalse();
    expect($details->osName)->toBeNull();
});
