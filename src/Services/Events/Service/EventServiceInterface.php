<?php

declare(strict_types=1);

namespace App\Services\Events\Service;

use App\Services\Core\Service\Interfaces\DomainEntityServiceInterface;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Ticket\Domain\TicketType;
use App\Services\User\Domain\User;

interface EventServiceInterface extends DomainEntityServiceInterface
{
    public function getOrganisationByHandle(string $handle): ?Organisation;

    public function saveOrganisation(Organisation $organisation): Organisation;

    /**
     * @param User $user
     * @return Organisation[]
     */
    public function getOrganisationsByManager(User $user): array;

    public function saveEvent(Event $event): Event;

    public function findEventBySlug(string $slug): ?Event;

    public function getTicketTypeByEventAndSlug(Event $event, string $slug): ?TicketType;

    public function saveTicketType(TicketType $ticketType): TicketType;

    public function findTicketType(int $id): ?TicketType;

    /**
     * @return Event[]
     */
    public function getUpcomingEvents(): iterable;
}
