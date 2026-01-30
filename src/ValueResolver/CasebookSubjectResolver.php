<?php

declare(strict_types=1);

namespace App\ValueResolver;

use App\Services\PuzzleInstance\Domain\Casebook\CasebookSubject;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

readonly class CasebookSubjectResolver implements ValueResolverInterface
{
    public function __construct(private CasebookSubjectRepository $casebookSubjectRepository)
    {
    }

    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $argumentType = $argument->getType();
        if ($argumentType !== CasebookSubject::class) {
            return [];
        }

        $id = $request->attributes->get('subjectId');

        if (!is_string($id)) {
            return [];
        }


        $subject = $this->casebookSubjectRepository->find($id);

        return $subject ? [$subject] : [];
    }
}
