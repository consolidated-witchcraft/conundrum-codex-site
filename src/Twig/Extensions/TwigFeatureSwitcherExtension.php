<?php

declare(strict_types=1);

namespace App\Twig\Extensions;

use App\Services\FeatureSwitcher\Service\Exceptions\UnknownFeatureHandleException;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use Twig\Attribute\AsTwigFunction;

class TwigFeatureSwitcherExtension
{
    public function __construct(private readonly FeatureSwitcherServiceInterface $featureSwitcherService) {}

    /**
     * @throws UnknownFeatureHandleException
     */
    #[AsTwigFunction('feature_enabled')]
    public function featureEnabled(string $featureHandle): bool {
        return $this->featureSwitcherService->isFeatureEnabled($featureHandle);
    }
}
