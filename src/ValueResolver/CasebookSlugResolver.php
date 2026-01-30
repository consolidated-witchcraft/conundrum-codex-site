<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\PuzzleInstance\Domain\Casebook\Casebook;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class CasebookSlugResolver implements ValueResolverInterface
{
    public function __construct(private CasebookRepository $casebookRepository)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== Casebook::class) {
            return [];
        }

        $slug = $request->attributes->get('casebookSlug');

        if (!is_string($slug)) {
            return [];
        }

        $casebook = $this->casebookRepository->findBySlug($slug);

        return $casebook ? [$casebook] : [];
    }
}
