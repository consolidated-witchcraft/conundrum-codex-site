<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleInstance\Dynamic\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\PuzzleInstance\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Services\Puzzle\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\Puzzle\Service\Exceptions\TemplateNotFoundException;
use App\Services\Puzzle\Service\Interfaces\PuzzleInstanceServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class DynamicPuzzleInstanceProvider implements ProviderInterface
{
    public function __construct(private readonly PuzzleInstanceServiceInterface $puzzleInstanceService) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        if (!$uriVariables['templateSlug']) {
            throw new BadRequestHttpException('You must provide a template slug.');
        }
        if (!$uriVariables['instanceCode'] || !is_string($uriVariables['instanceCode'])) {
            throw new BadRequestHttpException("Instance Code not set.");
        }

        $instanceCode = $uriVariables['instanceCode'];
        $templateSlug = $uriVariables['templateSlug'];
        try {
            $instance = $this->puzzleInstanceService->getInstanceByTemplateAndCode(templateSlug: $templateSlug, instanceCode: $instanceCode);
        } catch (TemplateNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        if (!$instance) {
            return null;
        }
        if (!$instance instanceof DynamicPuzzleInstanceInterface) {
            throw new BadRequestHttpException('Instance must implement DynamicPuzzleInstanceInterface.');
        }
        return DynamicPuzzleInstanceDto::makeFromDynamicPuzzleInstance($instance);
    }
}
