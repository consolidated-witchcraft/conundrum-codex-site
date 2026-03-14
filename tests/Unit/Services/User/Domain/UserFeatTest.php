<?php

declare(strict_types=1);

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\UserFeat;
use App\Services\User\Domain\ValueObjects\Rarity;

beforeEach(function () {
    $this->makeUserFeat = function (array $overrides = []): UserFeat {
        return new UserFeat(
            label: $overrides['label'] ?? 'Test Label',
            handle: $overrides['handle'] ?? 'TestHandle',
            description: $overrides['description'] ?? 'Test Description',
            iconClass: $overrides['iconClass'] ?? 'test-icon-class',
            rarity: $overrides['rarity'] ?? $this->createStub(Rarity::class),
            gamesMasterAwardable: $overrides['gamesMasterAwardable'] ?? false,
            id: $overrides['id'] ?? null
        );
    };
});

test('construct', function () {
    $label = "Test Label";
    $handle = "TestHandle";
    $description = "Test Description";
    $iconClass = "TestIconClass";
    $rarity = $this->createStub(Rarity::class);
    $id = 123;
    $feat = new UserFeat(
        label: $label,
        handle: $handle,
        description: $description,
        iconClass: $iconClass,
        rarity: $rarity,
        gamesMasterAwardable: false,
        id: $id
    );
    expect($feat->getLabel())->toEqual($label);
    expect($feat->getHandle())->toEqual($handle);
    expect($feat->getDescription())->toEqual($description);
    expect($feat->getIconClass())->toEqual($iconClass);
    expect($feat->getRarity())->toEqual($rarity);
    expect($feat->isGamesMasterAwardable())->toBeFalse();
    expect($feat->getId())->toEqual($id);
});

test('set label', function () {
    $newLabel = "Test Label 2";
    $feat = ($this->makeUserFeat)();
    $feat->setLabel($newLabel);
    expect($feat->getLabel())->toEqual($newLabel);
});

test('set description', function () {
    $newDescription = "Test Description 2";
    $feat = ($this->makeUserFeat)();
    $feat->setDescription($newDescription);
    expect($feat->getDescription())->toEqual($newDescription);
});

test('set icon class', function () {
    $newIconClass = "test-icon-class-2";
    $feat = ($this->makeUserFeat)();
    $feat->setIconClass($newIconClass);
    expect($feat->getIconClass())->toEqual($newIconClass);
});

test('set rarity', function () {
    $newRarity = $this->createConfiguredStub(Rarity::class, [
        'getKey' => 'TESTHANDLE2'
    ]);
    $feat = ($this->makeUserFeat)();
    $feat->setRarity($newRarity);
    expect($feat->getRarity())->toEqual($newRarity);
});

test('set games master awardable', function () {
    $feat = ($this->makeUserFeat)();
    self::assertFalse($feat->isGamesMasterAwardable());
    $feat->setGamesMasterAwardable(true);
    self::assertTrue($feat->isGamesMasterAwardable());
});

test('has description', function () {
    $feat = ($this->makeUserFeat)();
    self::assertTrue($feat->hasDescription());
});
