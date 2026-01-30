<?php

declare(strict_types=1);

namespace App\ApiResource\PuzzleTemplate\Provider;

use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProviderInterface;
use App\ApiResource\PuzzleTemplate\Dto\PuzzleTemplateDto;
use App\Services\PuzzleTemplate\Service\Interfaces\PuzzleTemplateServiceInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

readonly class PuzzleTemplateDtoProvider implements ProviderInterface
{
    public function __construct(private readonly PuzzleTemplateServiceInterface $puzzleTemplateService) {}
    public function provide(Operation $operation, array $uriVariables = [], array $context = []): object|array|null
    {

        if ($operation instanceof GetCollection) {
            $templatesArray = [];
            foreach ($this->puzzleTemplateService->getTemplates() as $template) {
                $templatesArray[] = PuzzleTemplateDto::makeFromTemplate($template);
            }
            return $templatesArray;
        }


        if ($uriVariables['templateSlug'] === null) {
            throw new BadRequestHttpException("Template slug is required.");
        }

        $template = $this->puzzleTemplateService->getTemplateBySlug($uriVariables['templateSlug']);
        return $template ? PuzzleTemplateDto::makeFromTemplate($template) : null;
    }


}
