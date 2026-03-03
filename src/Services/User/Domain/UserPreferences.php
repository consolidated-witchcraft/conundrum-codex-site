<?php

declare(strict_types=1);

namespace App\Services\User\Domain;

use App\Doctrine\Types\Exceptions\UnmappableObjectException;
use App\Services\User\Domain\ValueObjects\PreferredFontStack;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class UserPreferences
{
    /**
     * @throws UnmappableObjectException
     */
    public function __construct(
        #[ORM\Column(type: 'boolean', nullable: false)]
        private bool $hasAcceptedCookies = false,

        #[ORM\Column(type: 'boolean', nullable: false)]
        private bool $profilePublic = false,

        #[ORM\Column(type: 'preferred_font_stack', nullable: false, options: ['default' => 'regular'])]
        private ? PreferredFontStack $preferredFontStack = null
    ) {
        if ($preferredFontStack === null) {
            $this->preferredFontStack = PreferredFontStack::makeFromKey('regular');
        }
    }

    public function setProfilePublic(bool $profilePublic): void {
        $this->profilePublic = $profilePublic;
    }

    public function isProfilePublic(): bool {
        return $this->profilePublic;
    }

    public function getHasAcceptedCookies(): bool
    {
        return $this->hasAcceptedCookies;
    }

    public function setHasAcceptedCookies(bool $hasAcceptedCookies): void
    {
        $this->hasAcceptedCookies = $hasAcceptedCookies;
    }

    public function setPreferredFontStack(PreferredFontStack $preferredFontStack): void {
        $this->preferredFontStack = $preferredFontStack;
    }

    public function getPreferredFontStack(): PreferredFontStack
    {
        return $this->preferredFontStack;
    }
}
