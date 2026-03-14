<?php

declare(strict_types=1);
use App\Services\User\Domain\ValueObjects\Exceptions\UnmappedRarityException;
use App\Services\User\Domain\ValueObjects\Rarity;
use PHPUnit\Framework\Attributes\DataProvider;
dataset('provideRarityKeys', function () {
    yield 'common' => [
        'key' => 'c',
        'expectedLabel' => 'Common',
        'expectedClassSafeLabel' => 'common'
    ];

    yield 'uncommon' => [
        'key' => 'u',
        'expectedLabel' => 'Uncommon',
        'expectedClassSafeLabel' => 'uncommon'
    ];

    yield 'rare' => [
        'key' => 'r',
        'expectedLabel' => 'Rare',
        'expectedClassSafeLabel' => 'rare'
    ];

    yield 'epic' => [
        'key' => 'e',
        'expectedLabel' => 'Epic',
        'expectedClassSafeLabel' => 'epic'
    ];

    yield 'legendary' => [
        'key' => 'l',
        'expectedLabel' => 'Legendary',
        'expectedClassSafeLabel' => 'legendary'
    ];
});
test('make from rarity key', function (string $key, string $expectedLabel, string $expectedClassSafeLabel) {
    $rarity = Rarity::makeFromRarityKey($key);
    self::assertEquals($expectedLabel, $rarity->getLabel());
    self::assertEquals($key, $rarity->getKey());
    self::assertEquals($expectedClassSafeLabel, $rarity->getClassSafeLabel());
    self::assertEquals($key, (string) $rarity);
})->with('provideRarityKeys');
