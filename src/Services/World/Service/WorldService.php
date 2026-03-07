<?php

namespace App\Services\World\Service;

use App\Services\World\Domain\World;
use App\Services\World\Service\Interfaces\WorldServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class WorldService implements WorldServiceInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
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
}
