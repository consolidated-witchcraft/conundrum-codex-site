<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubjectClue;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectClueRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class CasebookClueResolver implements ValueResolverInterface
{
    public function __construct(private CasebookSubjectClueRepository $casebookSubjectClueRepository)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== CasebookSubjectClue::class) {
            return [];
        }

        $id = $request->attributes->get('clueId');

        if (!is_string($id)) {
            return [];
        }

        $casebook = $this->casebookSubjectClueRepository->find((int) $id);

        return $casebook ? [$casebook] : [];
    }
}
