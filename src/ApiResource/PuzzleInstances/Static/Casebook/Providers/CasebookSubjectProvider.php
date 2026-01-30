<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstances\Static\Casebook\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\PuzzleInstances\Static\Casebook\Dto\ApiCasebookSubjectDto;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @template-implements ProviderInterface<ApiCasebookSubjectDto>
 */
class CasebookSubjectProvider implements ProviderInterface
{
    public function __construct(
        private CasebookSubjectRepository $casebookSubjectRepository
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        if ($uriVariables['subject'] == null) {
            throw new UnprocessableEntityHttpException("Subject Id not found.");
        }

        if (!is_numeric($uriVariables['subject'])) {
            throw new UnprocessableEntityHttpException("Could not cast subject id to integer");
        }

        $subject = $this->casebookSubjectRepository->find((int) $uriVariables['subject']);

        if (!$subject) {
            return null;
        }


        return ApiCasebookSubjectDto::makeFromSubject($subject);

    }
}
