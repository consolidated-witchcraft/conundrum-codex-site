<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstances\Static\Casebook\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\Puzzles\Static\Casebook\Dto\ApiCasebookDto;
use App\Services\Puzzle\Domain\Casebook\Casebook;
use App\Services\Puzzle\Infrastructure\Casebook\Repository\CasebookRepository;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

/**
 * @template-implements ProviderInterface<ApiCasebookDto>
 */
class CasebookProvider implements ProviderInterface
{
    public function __construct(private CasebookRepository $casebookRepository) {}


    /**
     *
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['instanceCode']) {
            throw new UnprocessableEntityHttpException("Instance Code not set.");
        }
        if (is_string($uriVariables['instanceCode'])) {
            $instance = $this->casebookRepository->getInstance($uriVariables['instanceCode']);
            if ($instance instanceof Casebook) {
                return ApiCasebookDto::makeFromInstance($instance);
            }
        }
        return null;
    }
}
