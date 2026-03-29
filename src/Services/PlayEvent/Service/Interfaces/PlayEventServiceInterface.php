<?php

declare(strict_types=1);

namespace App\Services\PlayEvent\Service\Interfaces;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\PlayEvent\Domain\Enums\PlayEventTypeEnum;
use App\Services\PlayEvent\Domain\PlayEvent;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;

interface PlayEventServiceInterface extends DomainEntityServiceInterface
{
    public function record(
        User $user,
        PuzzleInstanceInterface $puzzleInstance,
        PlayEventTypeEnum $type,
        bool $flush = true
    ): PlayEvent;

    public function save(PlayEvent $playEvent, bool $flush = true): PlayEvent;

    public function find(int $id): ? PlayEvent;

    /**
     * @return PlayEvent[]
     */
    public function findAll(): array;

    /**
     * @return Collection<int, PlayEvent>
     */
    public function findByUser(User $user): Collection;

    /**
     * @return PlayEvent[]
     */
    public function findAllForUserBetweenDates(User $user, \DateTimeInterface $start, \DateTimeInterface $end): array;
}
