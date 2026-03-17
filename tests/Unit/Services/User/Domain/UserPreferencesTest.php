<?php

declare(strict_types=1);

use App\Services\User\Domain\UserPreferences;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;

test('construct defaults to regular font stack', function () {
    $preferences = new UserPreferences();

    self::assertFalse($preferences->getHasAcceptedCookies());
    self::assertFalse($preferences->isProfilePublic());
    self::assertSame('regular', $preferences->getPreferredFontStack()->getKey());
});

test('setters update preferences', function () {
    $preferences = new UserPreferences();
    $preferredFontStack = PreferredFontStack::makeFromKey('dyslexic');

    $preferences->setHasAcceptedCookies(true);
    $preferences->setProfilePublic(true);
    $preferences->setPreferredFontStack($preferredFontStack);

    self::assertTrue($preferences->getHasAcceptedCookies());
    self::assertTrue($preferences->isProfilePublic());
    self::assertSame($preferredFontStack, $preferences->getPreferredFontStack());
});
