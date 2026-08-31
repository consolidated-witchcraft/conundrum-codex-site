<?php

declare(strict_types=1);

use App\Services\Core\Domain\Exceptions\InvalidHandleException;
use App\Services\User\Domain\ValueObjects\UserAccountType;

test('construct', function () {
    $userAccountType = new UserAccountType(
        label: 'Premium',
        handle: 'premium',
        description: 'Premium users can run more games.',
        id: 5,
    );

    self::assertSame('Premium', $userAccountType->getLabel());
    self::assertSame('premium', $userAccountType->getHandle());
    self::assertSame('Premium users can run more games.', $userAccountType->getDescription());
    self::assertSame('Premium', (string) $userAccountType);
    self::assertTrue(UserAccountType::hasDescription());
    self::assertSame(5, $userAccountType->getId());
});

test('set description updates state', function () {
    $userAccountType = new UserAccountType(
        label: 'Premium',
        handle: 'premium',
        description: 'Premium users can run more games.',
    );

    $userAccountType->setDescription('Updated description.');

    self::assertSame('Updated description.', $userAccountType->getDescription());
});

test('invalid handle is rejected', function () {
    $this->expectException(InvalidHandleException::class);

    new UserAccountType(
        label: 'Premium',
        handle: 'premium-tier',
        description: 'Premium users can run more games.',
    );
});
