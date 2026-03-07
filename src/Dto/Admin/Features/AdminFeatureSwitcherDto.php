<?php

declare(strict_types=1);

namespace App\Dto\Admin\Features;

use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use Symfony\Component\Validator\Constraints as Assert;
class AdminFeatureSwitcherDto
{
    public function __construct(

        #[Assert\NotBlank]
        public ? string $label = null,

        #[Assert\NotBlank]
        public ? string $handle = null,
        public bool $enabled = false
    ) {}

    public static function makeFromFeatureSwitcher(FeatureSwitcher $featureSwitcher): static {
        return new static(
            label: $featureSwitcher->getLabel(),
            handle: $featureSwitcher->getHandle(),
            enabled: $featureSwitcher->isEnabled()
        );
    }
}
