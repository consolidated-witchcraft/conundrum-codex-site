<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstances\Dynamic\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\Services\Puzzle\Service\Interfaces\PuzzleInstanceServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class DynamicPuzzleInstanceProvider implements ProviderInterface
{
    public function __construct(private readonly PuzzleInstanceServiceInterface $puzzleInstanceService) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['instanceCode']) {
            throw new BadRequestHttpException("Instance Code not set.");
        }
        if (is_string($uriVariables['instanceCode'])) {
            $instanceCode = $uriVariables['instanceCode'];
        }
    }
}
