<?php

declare(strict_types=1);

namespace App\Services\BlockedEmailAddress\Service;

use App\Services\BlockedEmailAddress\Domain\BlockedEmailAddress;
use App\Services\BlockedEmailAddress\Infrastructure\BlockedEmailAddressRepository;
use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class BlockedEmailAddressService extends AbstractDomainEntityService implements DomainEntityServiceInterface
{
    public function __construct(
        private BlockedEmailAddressRepository $blockedEmailAddressRepository,
        EntityManagerInterface $entityManager
    ) {
        parent::__construct($entityManager);
    }

    public function findByEmail(string $email): ? BlockedEmailAddress {
        return $this->blockedEmailAddressRepository->findByEmail($email);
    }

    public function save(BlockedEmailAddress $blockedEmailAddress, bool $flush = true): BlockedEmailAddress {
        $this->entityManager->persist($blockedEmailAddress);
        if ($flush) {
            $this->flush();
        }

        return $blockedEmailAddress;
    }
}
