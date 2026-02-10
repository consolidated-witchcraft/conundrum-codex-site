<?php

declare(strict_types=1);

namespace App\Services\Events\Service;

use App\Services\Events\Domain\Organisation;
use App\Services\Events\Infrastructure\OrganisationRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class EventService implements EventServiceInterface
{
    public function __construct(
        private readonly OrganisationRepository $organisationRepository,
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function getOrganisationByHandle(string $handle, bool $includeDeactivated = false): ?Organisation
    {
        return $this->organisationRepository->findOneBy(['handle' => $handle]);
    }

    public function saveOrganisation(Organisation $organisation): Organisation
    {
        $this->entityManager->persist($organisation);
        $this->entityManager->flush();
        return $organisation;
    }

    /**
     * @param User $user
     * @return Organisation[]
     */
    public function getOrganisationsByManager(User $user, bool $includeDeactivated = false): array
    {
        return $this->organisationRepository->getOrganisationsByManager($user, $includeDeactivated);
    }
}
