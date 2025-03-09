<?php

declare(strict_types=1);

use App\Enums\AttachmentType;

it('can get description for enum value', function () {
    expect(AttachmentType::Avatar->description())->toBe('Avatar');
    expect(AttachmentType::Generic->description())->toBe('Generic');
});

it('can check if enum value equals another', function () {
    // Compare with enum instance
    expect(AttachmentType::Avatar->is(AttachmentType::Avatar))->toBeTrue();
    expect(AttachmentType::Avatar->is(AttachmentType::Generic))->toBeFalse();

    // Compare with string value
    expect(AttachmentType::Avatar->is('avatar'))->toBeTrue();
    expect(AttachmentType::Avatar->is('generic'))->toBeFalse();

    // Check isNot
    expect(AttachmentType::Avatar->isNot(AttachmentType::Generic))->toBeTrue();
    expect(AttachmentType::Avatar->isNot('generic'))->toBeTrue();
    expect(AttachmentType::Avatar->isNot(AttachmentType::Avatar))->toBeFalse();
});

it('can check if enum is in a list of values', function () {
    $list = [AttachmentType::Generic, 'avatar'];

    expect(AttachmentType::Avatar->in($list))->toBeTrue();
    expect(AttachmentType::Generic->in($list))->toBeTrue();

    $emptyList = [];
    expect(AttachmentType::Avatar->in($emptyList))->toBeFalse();

    $invalidList = ['invalid', 'values'];
    expect(AttachmentType::Avatar->in($invalidList))->toBeFalse();
});

it('can check if enum is not in a list of values', function () {
    $list = [AttachmentType::Generic];

    expect(AttachmentType::Avatar->notIn($list))->toBeTrue();
    expect(AttachmentType::Generic->notIn($list))->toBeFalse();

    $emptyList = [];
    expect(AttachmentType::Avatar->notIn($emptyList))->toBeTrue();
});

it('can check if enum has a specific value', function () {
    expect(AttachmentType::hasValue('avatar'))->toBeTrue();
    expect(AttachmentType::hasValue('generic'))->toBeTrue();
    expect(AttachmentType::hasValue('invalid'))->toBeFalse();
    expect(AttachmentType::hasValue(null))->toBeFalse();

    // Check with enum instance
    expect(AttachmentType::hasValue(AttachmentType::Avatar))->toBeTrue();
});

it('can coerce value to enum', function () {
    // Valid string
    expect(AttachmentType::coerce('avatar'))->toBe(AttachmentType::Avatar);

    // Existing enum instance
    $avatar = AttachmentType::Avatar;
    expect(AttachmentType::coerce($avatar))->toBe($avatar);

    // Invalid value
    expect(AttachmentType::coerce('invalid'))->toBeNull();

    // Null
    expect(AttachmentType::coerce(null))->toBeNull();
});

it('can get enum values as select array', function () {
    $selectArray = AttachmentType::asSelectArray();

    expect($selectArray)->toBeArray();
    expect($selectArray)->toHaveCount(2);
    expect($selectArray)->toHaveKey('avatar');
    expect($selectArray)->toHaveKey('generic');
    expect($selectArray['avatar'])->toBe('Avatar');
    expect($selectArray['generic'])->toBe('Generic');
});

it('can collect all enum cases', function () {
    $collection = AttachmentType::collect();

    expect($collection)->toBeCollection();
    expect($collection)->toHaveCount(2);
    expect($collection[0])->toBe(AttachmentType::Avatar);
    expect($collection[1])->toBe(AttachmentType::Generic);
});
