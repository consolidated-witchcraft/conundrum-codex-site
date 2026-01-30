<?php

declare(strict_types=1);

namespace App\Twig\Extensions;

use Twig\Attribute\AsTwigFunction;

class TwigDateExtensions
{
    private const string FRIENDLY_DATE_FORMAT = 'H:i, l, jS F, Y';
    #[AsTwigFunction('friendly_date', needsEnvironment: false, isSafe: ['html'])]
    public function getFriendlyDate(\DateTimeInterface $date): string {
        return $date->format(self::FRIENDLY_DATE_FORMAT);
    }
}
