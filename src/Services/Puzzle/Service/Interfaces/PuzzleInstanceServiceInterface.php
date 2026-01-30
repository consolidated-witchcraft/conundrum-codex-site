<?php

declare(strict_types=1);

namespace App\Services\Puzzle\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainServiceInterface;
use App\Services\Game\Domain\Game;
use App\Services\Puzzle\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\Puzzle\Domain\Interfaces\StaticPuzzleInstanceInterface;
use App\Services\Puzzle\Service\Exceptions\TemplateNotFoundException;
use Doctrine\Common\Collections\ArrayCollection;

interface PuzzleInstanceServiceInterface extends DomainServiceInterface
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
}
