<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Static\Casebook\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\PuzzleInstance\Static\Casebook\Dto\ApiCasebookSubjectDto;
use App\Services\PuzzleInstance\Infrastructure\Casebook\Repository\CasebookSubjectRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @template-implements ProviderInterface<ApiCasebookSubjectDto>
 */
class CasebookSubjectProvider implements ProviderInterface
{
    public function __construct(
        private readonly CasebookSubjectRepository $casebookSubjectRepository
    ) {}


    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!isset($uriVariables['instanceCode']) || !is_string($uriVariables['instanceCode']) || $uriVariables['instanceCode'] === '') {
            throw new BadRequestHttpException('Instance code not found.');
        }

        if ($uriVariables['subject'] == null) {
            throw new BadRequestHttpException("Subject Id not found.");
        }

        if (!is_numeric($uriVariables['subject'])) {
            throw new BadRequestHttpException("Could not cast subject id to integer");
        }

        $subject = $this->casebookSubjectRepository->findOneBySubjectIdAndInstanceCode(
            (int) $uriVariables['subject'],
            $uriVariables['instanceCode']
        );

        if (!$subject) {
            return null;
        }

        return ApiCasebookSubjectDto::makeFromSubject($subject);

    }
}
