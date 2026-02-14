<?php

declare(strict_types=1);

namespace App\Services\Core\Service;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

abstract class AbstractDomainEntityService implements DomainEntityServiceInterface
{
    public function __construct(
        protected readonly EntityManagerInterface $entityManager
    ) {}

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
