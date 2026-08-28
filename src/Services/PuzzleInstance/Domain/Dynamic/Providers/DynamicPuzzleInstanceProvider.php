<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Domain\Dynamic\Providers;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\PuzzleInstance\Dynamic\ApiDynamicPuzzleInstanceDto;
use App\Services\PuzzleInstance\Domain\Dynamic\Dto\DynamicPuzzleInstanceDto;
use App\Services\PuzzleInstance\Domain\Dynamic\Fields\Exceptions\UnmappableDynamicFieldTypeException;
use App\Services\PuzzleInstance\Domain\Interfaces\DynamicPuzzleInstanceInterface;
use App\Services\PuzzleInstance\Service\Exceptions\AttemptingToGenerateDtoForStaticPuzzleException;
use App\Services\PuzzleInstance\Service\Interfaces\PuzzleInstanceEntityServiceInterface;
use App\Services\PuzzleTemplate\Domain\Exceptions\PuzzleTemplateNotMappedException;
use App\Services\PuzzleTemplate\Service\Exceptions\TemplateNotFoundException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/** @implements ProviderInterface<ApiDynamicPuzzleInstanceDto> */
readonly class DynamicPuzzleInstanceProvider implements ProviderInterface
{
    public function __construct(private PuzzleInstanceEntityServiceInterface $puzzleInstanceService) {}

    /**
     * @throws PuzzleTemplateNotMappedException
     * @throws UnmappableDynamicFieldTypeException
     * @throws AttemptingToGenerateDtoForStaticPuzzleException
     */
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {
        $templateSlug = $uriVariables['templateSlug'] ?? null;
        if (!is_string($templateSlug) || $templateSlug === '') {
            throw new BadRequestHttpException('You must provide a template slug.');
        }
        $instanceCode = $uriVariables['instanceCode'] ?? null;
        if (!is_string($instanceCode) || $instanceCode === '') {
            throw new BadRequestHttpException("Instance Code not set.");
        }

        try {
            $instance = $this->puzzleInstanceService->getInstanceByTemplateAndCode(templateSlug: $templateSlug, instanceCode: $instanceCode);
        } catch (TemplateNotFoundException $e) {
            throw new NotFoundHttpException($e->getMessage());
        }
        if (!$instance) {
            return null;
        }
        if (!$instance->isPublished()) {
            return null;
        }
        if (!$instance instanceof DynamicPuzzleInstanceInterface) {
            throw new BadRequestHttpException('Instance must implement DynamicPuzzleInstanceInterface.');
        }
        return ApiDynamicPuzzleInstanceDto::makeFromPuzzleInstance($instance);
    }
}
