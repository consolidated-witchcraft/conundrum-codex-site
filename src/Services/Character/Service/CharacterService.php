<?php

declare(strict_types=1);

namespace App\Services\Character\Service;

use App\Services\Character\Domain\Character;
use App\Services\Character\Infrastructure\CharacterRepository;
use App\Services\Character\Service\Interfaces\CharacterServiceInterface;
use App\Services\Core\Service\AbstractDomainEntityService;
use Doctrine\ORM\EntityManagerInterface;

class CharacterService extends AbstractDomainEntityService implements CharacterServiceInterface
{
    public function __construct(
        private readonly CharacterRepository $characterRepository,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct(entityManager: $entityManager);
    }

    public function save(Character $character, bool $flush = true): Character
    {
        $this->entityManager->persist($character);

        if ($flush) {
            $this->flush();
        }

        return $character;
    }

    public function find(int $id): ?Character
    {
        return $this->characterRepository->find($id);
    }

    public function delete(Character $character, bool $flush = true): void
    {
        $this->entityManager->remove($character);

        if ($flush) {
            $this->flush();
        }
    }
}
