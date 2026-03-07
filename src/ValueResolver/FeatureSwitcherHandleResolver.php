<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\FeatureSwitcher\Domain\FeatureSwitcher;
use App\Services\FeatureSwitcher\Service\Interfaces\FeatureSwitcherServiceInterface;
use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class FeatureSwitcherHandleResolver implements ValueResolverInterface
{
    public function __construct(private FeatureSwitcherServiceInterface $featureSwitcherService)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== FeatureSwitcher::class) {
            return [];
        }

        $handle = $request->attributes->get('featureHandle');

        if (!is_string($handle)) {
            return [];
        }

        $featureSwitcher = $this->featureSwitcherService->getByHandle($handle);

        return $featureSwitcher ? [$featureSwitcher] : [];
    }
}
