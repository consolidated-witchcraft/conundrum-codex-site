<?php

declare(strict_types=1);

namespace App\Services\PuzzleInstance\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\PuzzleTemplate\Domain\PuzzleTemplate;
use App\Services\PuzzleTemplate\Service\Exceptions\TemplateNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;

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

    /**
     * @return ArrayCollection<int, StaticPuzzleInstanceInterface>
     */
    public function getStaticPuzzleInstancesForGame(Game $game): ArrayCollection;

    public function generateNewInstanceCode(PuzzleTemplate $puzzleTemplate): string;
}
