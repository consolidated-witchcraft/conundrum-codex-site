<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\Puzzle\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleInstanceServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class PuzzleInstanceResolver implements ValueResolverInterface
{
    public function __construct(
        private PuzzleInstanceServiceInterface $puzzleInstanceService
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if (
            !in_array($argumentType, [
                DynamicPuzzleInstanceInterface::class,
                PuzzleInstanceInterface::class,
            ])
        ) {
            return [];
        }


        $instanceCode = $request->attributes->get('instanceCode');
        $templateSlug = $request->attributes->get('templateSlug');


        if (!is_string($instanceCode) || !is_string($templateSlug)) {
            return [];
        }

        $instance = $this->puzzleInstanceService->getInstanceByTemplateAndCode(
            templateSlug: $templateSlug,
            instanceCode: $instanceCode
        );

        return $instance ? [$instance] : [];
    }
}
