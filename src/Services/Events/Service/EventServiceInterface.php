<?php

declare(strict_types=1);

namespace App\Services\Events\Service;

use App\Services\Core\Service\Interfaces\DomainServiceInterface;
use App\Services\Events\Domain\Organisation;
use App\Services\User\Domain\User;

interface EventServiceInterface extends DomainServiceInterface
{
    public function getOrganisationByHandle(string $handle): ?Organisation;

    public function saveOrganisation(Organisation $organisation): Organisation;

    /**
     * @param User $user
     * @return Organisation[]
     */
    public function getOrganisationsByManager(User $user): array;
}
