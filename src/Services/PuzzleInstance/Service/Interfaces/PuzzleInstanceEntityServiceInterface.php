<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Exceptions\TemplateNotFoundException;

interface PuzzleInstanceEntityServiceInterface extends DomainEntityServiceInterface
{
    /**
     * @param string $templateSlug
     * @param string $instanceCode
     * @return PuzzleInstanceInterface|null
     * @throws TemplateNotFoundException
     */
    public function getInstanceByTemplateAndCode(string $templateSlug, string $instanceCode): ?PuzzleInstanceInterface;

    public function saveInstance(PuzzleInstanceInterface $instance): void;

    public function deleteInstance(PuzzleInstanceInterface $instance): void;

    public function generateNewInstanceCode(PuzzleTemplate $puzzleTemplate): string;
}
