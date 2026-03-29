<?php

declare(strict_types=1);

namespace App\Services\PlayEvent\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\PlayEvent\Domain\Enums\PlayEventTypeEnum;
use App\Services\PlayEvent\Domain\PlayEvent;
use App\Services\PlayEvent\Infrastructure\PlayEventRepository;
use App\Services\PlayEvent\Service\Interfaces\PlayEventServiceInterface;
use App\Services\PuzzleInstance\Domain\Interfaces\PuzzleInstanceInterface;
use App\Services\User\Domain\User;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;

class PlayEventService extends AbstractDomainEntityService implements PlayEventServiceInterface
{
    public function __construct(
        private readonly PlayEventRepository $playEventRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function record(User $user, PuzzleInstanceInterface $puzzleInstance, PlayEventTypeEnum $type, bool $flush = true): PlayEvent
    {
        $playEvent = new PlayEvent(
            user: $user,
            type: $type,
            puzzleInstanceCode: $puzzleInstance->getInstanceCode(),
            puzzleTemplateSlug: $puzzleInstance->getTemplateSlug()
        );

        return $this->save($playEvent, $flush);
    }

    public function save(PlayEvent $playEvent, bool $flush = true): PlayEvent
    {
        $this->entityManager->persist($playEvent);

        if ($flush) {
            $this->flush();
        }

        return $playEvent;
    }

    public function find(int $id): ?PlayEvent
    {
        return $this->playEventRepository->find($id);
    }

    /**
     * @return PlayEvent[]
     */
    public function findAll(): array
    {
        return $this->playEventRepository->findAll();
    }

    /**
     * @return Collection<int, PlayEvent>
     */
    public function findByUser(User $user): Collection
    {
        return $this->playEventRepository->findByUser($user);
    }

    /**
     * @return PlayEvent[]
     */
    public function findAllForUserBetweenDates(User $user, \DateTimeInterface $start, \DateTimeInterface $end): array
    {
        return $this->playEventRepository->findAllForUserBetweenDates($user, $start, $end);
    }
}
