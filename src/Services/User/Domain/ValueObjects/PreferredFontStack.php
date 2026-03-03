<?php

declare(strict_types=1);

namespace App\Services\User\Domain\ValueObjects;

use App\Services\Core\Domain\AbstractDoctrineEnum;

class PreferredFontStack extends AbstractDoctrineEnum
{
    protected const array KNOWN_KEYS = [
        'regular' => 'Regular',
        'sans-serif' => 'Sans Serif',
        'dyslexic' => 'Dyslexic',
    ];

    public static function getAvailableFontStacks(): array {
        $availableStacks = [];
        foreach (static::KNOWN_KEYS as $key => $label) {
            $availableStacks[$key] = static::makeFromKey($key);
        }
        return $availableStacks;
    }
}
