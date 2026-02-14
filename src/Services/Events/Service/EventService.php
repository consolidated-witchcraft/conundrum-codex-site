<?php

declare(strict_types=1);

namespace App\Services\Events\Service;

use App\Services\Core\Service\AbstractDomainEntityService;
use App\Services\Events\Domain\Event;
use App\Services\Events\Domain\Organisation;
use App\Services\Events\Infrastructure\EventRepository;
use App\Services\Events\Infrastructure\OrganisationRepository;
use App\Services\Ticket\Domain\TicketType;
use App\Services\Ticket\Infrastructure\TicketTypeRepository;
use App\Services\User\Domain\User;
use Doctrine\ORM\EntityManagerInterface;

class EventService extends AbstractDomainEntityService implements EventServiceInterface
{
    public function __construct(
        private readonly OrganisationRepository $organisationRepository,
        private readonly EventRepository $eventRepository,
        public readonly TicketTypeRepository $ticketTypeRepository,
        EntityManagerInterface $entityManager,
    ) {
        parent::__construct($entityManager);
    }

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

    public function saveEvent(Event $event): Event
    {
        $this->entityManager->persist($event);
        $this->entityManager->flush();
        return $event;
    }

    public function findEventBySlug(string $slug): ?Event
    {
        return $this->eventRepository->findOneBy(['eventSlug' => $slug]);
    }

    public function getTicketTypeByEventAndSlug(Event $event, string $slug): ?TicketType
    {
        return $this->ticketTypeRepository->findOneBy(['event' => $event, 'slug' => $slug]);
    }

    public function saveTicketType(TicketType $ticketType): TicketType {
        $this->entityManager->persist($ticketType);
        $this->entityManager->flush();
        return $ticketType;
    }

    public function findTicketType(int $id): ?TicketType
    {
        return $this->ticketTypeRepository->find($id);
    }

    public function getUpcomingEvents(): iterable
    {
        return $this->eventRepository->getUpcomingEvents();
    }
}
