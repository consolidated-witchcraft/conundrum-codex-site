<?php

namespace App\Dto\Visitor\User;

use App\Services\User\Domain\UserPreferences;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;

class UserPreferencesEditDto
{
    private function __construct(
        public bool $hasAcceptedCookies,
        public bool $profilePublic,
        public PreferredFontStack $preferredFontStack
    ) {}

    public static function makeFromUserPreferences(UserPreferences $userPreferences): UserPreferencesEditDto {
        return new static(
            hasAcceptedCookies: $userPreferences->getHasAcceptedCookies(),
            profilePublic: $userPreferences->isProfilePublic(),
            preferredFontStack: $userPreferences->getPreferredFontStack(),
        );
    }
}
