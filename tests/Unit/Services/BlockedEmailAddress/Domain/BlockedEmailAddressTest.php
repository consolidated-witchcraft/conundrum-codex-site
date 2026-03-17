<?php

declare(strict_types=1);

use App\Services\BlockedEmailAddress\Domain\BlockedEmailAddress;

test('construct', function () {
    $blockedEmailAddress = new BlockedEmailAddress(
        emailAddress: 'blocked@example.com',
        blockReason: 'Test reason',
        id: 42,
    );

    self::assertSame('blocked@example.com', $blockedEmailAddress->getEmailAddress());
    self::assertSame('Test reason', $blockedEmailAddress->getBlockReason());
    self::assertSame(42, $blockedEmailAddress->getId());
});

test('setters update state', function () {
    $blockedEmailAddress = new BlockedEmailAddress(
        emailAddress: 'blocked@example.com',
        blockReason: 'Test reason',
    );

    $blockedEmailAddress->setEmailAddress('other@example.com');
    $blockedEmailAddress->setBlockReason('Updated reason');

    self::assertSame('other@example.com', $blockedEmailAddress->getEmailAddress());
    self::assertSame('Updated reason', $blockedEmailAddress->getBlockReason());
});
