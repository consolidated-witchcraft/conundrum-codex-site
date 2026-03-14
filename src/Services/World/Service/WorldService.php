<?php

declare(strict_types=1);

namespace App\Services\World\Service;

use App\Services\World\Domain\DTOs\WorldDateDto;
use App\Services\World\Domain\World;
use App\Services\World\Infrastructure\WorldRepository;
use App\Services\World\Service\Exceptions\InvalidWorldDateDtoException;
use App\Services\World\Service\Exceptions\UnconfiguredWorldCalendarException;
use App\Services\World\Service\Interfaces\WorldCalendarServiceInterface;
use App\Services\World\Service\Interfaces\WorldServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

readonly class WorldService implements WorldServiceInterface
{
    public function __construct(
        private WorldCalendarServiceInterface $worldCalendarService,
        private WorldRepository               $worldRepository,
        private EntityManagerInterface        $entityManager
    ) {}

    public function flush(): void
    {
        $this->entityManager->flush();
    }

    public function saveWorld(World $world, bool $flush = true): World
    {
        $this->entityManager->persist($world);

        if ($flush) {
            $this->flush();
        }
        return $world;
    }

    /**
     * @throws UnconfiguredWorldCalendarException
     */
    public function timeStampToWorldDate(int $timestamp, World $world): WorldDateDto
    {
        return $this->worldCalendarService->timeStampToWorldDate($timestamp, $world);
    }

    /**
     * @throws InvalidWorldDateDtoException
     * @throws UnconfiguredWorldCalendarException
     */
    public function worldDateToTimestamp(WorldDateDto $worldDate, World $world): int
    {
        return $this->worldCalendarService->worldDateToTimestamp($worldDate, $world);
    }

    public function findBySlug(string $slug): ?World
    {
        return $this->worldRepository->findOneBy(['slug' => $slug]);
    }
}
