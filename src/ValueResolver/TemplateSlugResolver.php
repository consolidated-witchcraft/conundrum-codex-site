<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class TemplateSlugResolver implements ValueResolverInterface
{
    public function __construct(
        private PuzzleTemplateServiceInterface $puzzleService
    ) {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== PuzzleTemplate::class) {
            return [];
        }

        $slug = $request->attributes->get('templateSlug');

        if (!is_string($slug)) {
            return [];
        }

        $casebook = $this->puzzleService->getTemplateBySlug($slug);

        return $casebook ? [$casebook] : [];
    }
}
