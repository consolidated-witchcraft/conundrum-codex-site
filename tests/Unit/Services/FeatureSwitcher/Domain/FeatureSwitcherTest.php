<?php

declare(strict_types=1);

use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;

test('construct', function () {
    $featureSwitcher = new FeatureSwitcher(
        handle: 'world_builder',
        label: 'World Builder',
        enabled: true,
        id: 7,
    );

    self::assertSame('world_builder', $featureSwitcher->getHandle());
    self::assertSame('World Builder', $featureSwitcher->getLabel());
    self::assertTrue($featureSwitcher->isEnabled());
    self::assertSame(7, $featureSwitcher->getId());
});

test('enable and disable update state', function () {
    $featureSwitcher = new FeatureSwitcher(
        handle: 'world_builder',
        label: 'World Builder',
    );

    self::assertFalse($featureSwitcher->isEnabled());

    $featureSwitcher->enable();
    self::assertTrue($featureSwitcher->isEnabled());

    $featureSwitcher->disable();
    self::assertFalse($featureSwitcher->isEnabled());
});
